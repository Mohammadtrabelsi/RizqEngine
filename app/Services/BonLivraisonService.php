<?php

namespace App\Services;

use App\Exceptions\ConversionException;
use App\Models\BonLivraison;
use App\Models\BonLivraisonDetails;
use App\Models\Commande;
use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Business operations for the Bon de Livraison (BL) — the delivery note created
 * from a Commande, and the last (optional) document before invoicing (Facture)
 * on the shorter Devis → Commande → Bon de Livraison → Facture path.
 *
 * Like {@see CommandeService}, the conversion copies the raw integer-cent columns
 * verbatim and is guarded (row lock + explicit check + unique commande_id index)
 * against duplicate conversion. Producing a delivery note does not itself move
 * stock — stock is handled by the optional Facture through the normal Sale flow,
 * exactly as for the Devis → … → Commande → Facture path — so goods are never
 * deducted twice.
 */
class BonLivraisonService
{
    /**
     * Paginate bons de livraison, optionally filtered by reference/customer
     * name, status, and/or a date range.
     *
     * @param  array{status?: string, date_from?: string, date_to?: string}  $filters
     */
    public function paginate(?string $search = null, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return BonLivraison::query()
            ->with('commande')
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('reference', 'like', $term)
                    ->orWhere('customer_name', 'like', $term);
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('date', '<=', $date))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Load a bon de livraison with the relations needed by its detail view and
     * return the attached customer.
     *
     * @return array{0: BonLivraison, 1: Customer}
     */
    public function showData(BonLivraison $bonLivraison): array
    {
        $bonLivraison->load(['bonLivraisonDetails.product', 'commande.quotation', 'commande.bonCommande.quotation', 'sale']);

        return [$bonLivraison, Customer::findOrFail($bonLivraison->customer_id)];
    }

    public function delete(BonLivraison $bonLivraison): void
    {
        $commande = $bonLivraison->commande;

        $bonLivraison->delete();

        // Deleting a delivery note rolls the order's shipping progress back.
        $commande?->syncShippingStatus();
    }

    /**
     * Line monetary columns allocated proportionally to the shipped quantity.
     *
     * @var string[]
     */
    private const LINE_AMOUNT_COLUMNS = [
        'sub_total',
        'product_discount_amount',
        'product_tax_amount',
    ];

    /**
     * Header monetary columns allocated proportionally to the shipped value.
     *
     * @var string[]
     */
    private const HEADER_AMOUNT_COLUMNS = [
        'tax_amount',
        'discount_amount',
        'shipping_amount',
        'total_amount',
    ];

    /**
     * Transform a Commande into a Bon de Livraison (delivery note), supporting
     * partial shipping: only the requested quantities are delivered and the
     * remainder can be shipped later on further delivery notes.
     *
     * Monetary amounts are allocated proportionally to the shipped quantity
     * (lines) and shipped value (header), with the shipment that completes a
     * line — or the whole order — absorbing any rounding remainder so that the
     * sum of all delivery notes reconciles exactly with the Commande. A single
     * full shipment therefore carries the Commande's amounts verbatim.
     *
     * @param  array<int, int>|null  $quantities  commande_detail id => quantity to
     *                                             ship; null ships every remaining
     *                                             quantity.
     *
     * @throws ConversionException when nothing remains to deliver or a requested
     *                             quantity exceeds what is still outstanding.
     */
    public function createFromCommande(Commande $commande, ?array $quantities = null): BonLivraison
    {
        return DB::transaction(function () use ($commande, $quantities) {
            // Serialize concurrent conversions of the same Commande.
            $commande = Commande::lockForUpdate()->findOrFail($commande->id);
            $commande->load('commandeDetails');

            $remaining = $commande->remainingQuantities();

            // Resolve the quantity to ship for each ordered line.
            $toShip = [];
            foreach ($commande->commandeDetails as $detail) {
                $left = $remaining[$detail->id] ?? 0;
                $qty = $quantities === null
                    ? $left
                    : (int) ($quantities[$detail->id] ?? 0);

                if ($qty < 0) {
                    throw new ConversionException(trans('bonlivraison.invalid-quantity'));
                }

                if ($qty > $left) {
                    throw new ConversionException(trans('bonlivraison.quantity-exceeds-remaining', [
                        'product' => $detail->product_name,
                    ]));
                }

                if ($qty > 0) {
                    $toShip[$detail->id] = $qty;
                }
            }

            if ($toShip === []) {
                throw new ConversionException(
                    trans('bonlivraison.nothing-to-deliver', ['reference' => $commande->reference])
                );
            }

            // Does this shipment bring every line to fully delivered?
            $completesOrder = true;
            foreach ($commande->commandeDetails as $detail) {
                if (($remaining[$detail->id] ?? 0) !== ($toShip[$detail->id] ?? 0)) {
                    $completesOrder = false;
                    break;
                }
            }

            $orderedSubTotal = (int) $commande->commandeDetails->sum(fn ($detail) => $detail->getRawOriginal('sub_total'));
            $shippedSubTotal = (int) $commande->commandeDetails
                ->filter(fn ($detail) => isset($toShip[$detail->id]))
                ->sum(fn ($detail) => (int) $detail->getRawOriginal('sub_total'));

            $bonLivraison = new BonLivraison;
            $bonLivraison->date = now()->format('Y-m-d');
            $bonLivraison->commande_id = $commande->id;
            $bonLivraison->customer_id = $commande->customer_id;
            $bonLivraison->customer_name = $commande->customer_name;
            $bonLivraison->status = BonLivraison::STATUS_PENDING;
            $bonLivraison->tax_percentage = $commande->getRawOriginal('tax_percentage');
            $bonLivraison->discount_percentage = $commande->getRawOriginal('discount_percentage');
            $bonLivraison->note = $commande->getRawOriginal('note');

            foreach (self::HEADER_AMOUNT_COLUMNS as $column) {
                $bonLivraison->{$column} = $this->allocateHeaderAmount(
                    $commande,
                    $column,
                    $shippedSubTotal,
                    $orderedSubTotal,
                    $completesOrder
                );
            }

            $bonLivraison->save();

            foreach ($commande->commandeDetails as $detail) {
                if (! isset($toShip[$detail->id])) {
                    continue;
                }

                $this->createLine($bonLivraison, $detail, $toShip[$detail->id], $remaining[$detail->id] ?? 0);
            }

            // Reflect the new delivery progress on the originating order.
            $commande->syncShippingStatus();

            return $bonLivraison->refresh();
        });
    }

    /**
     * Allocate a header amount column for this shipment. The shipment that
     * completes the order takes the exact outstanding balance so the sum across
     * delivery notes equals the Commande amount; otherwise it is prorated by the
     * shipped sub-total.
     */
    private function allocateHeaderAmount(
        Commande $commande,
        string $column,
        int $shippedSubTotal,
        int $orderedSubTotal,
        bool $completesOrder
    ): int {
        $total = (int) $commande->getRawOriginal($column);

        if ($completesOrder) {
            $alreadyAllocated = (int) $commande->bonLivraisons()->sum($column);

            return $total - $alreadyAllocated;
        }

        if ($orderedSubTotal <= 0) {
            return 0;
        }

        return intdiv($total * $shippedSubTotal, $orderedSubTotal);
    }

    /**
     * Create a delivery-note line for the shipped quantity, prorating its
     * monetary columns and absorbing rounding remainder on the shipment that
     * completes the ordered line.
     */
    private function createLine(BonLivraison $bonLivraison, $detail, int $shipQty, int $remainingForLine): void
    {
        $orderedQty = (int) $detail->getRawOriginal('quantity');
        $completesLine = $shipQty === $remainingForLine;

        $line = [
            'bon_livraison_id' => $bonLivraison->id,
            'commande_detail_id' => $detail->id,
            'product_id' => $detail->getRawOriginal('product_id'),
            'product_name' => $detail->getRawOriginal('product_name'),
            'product_code' => $detail->getRawOriginal('product_code'),
            'quantity' => $shipQty,
            'price' => $detail->getRawOriginal('price'),
            'unit_price' => $detail->getRawOriginal('unit_price'),
            'product_discount_type' => $detail->getRawOriginal('product_discount_type'),
        ];

        foreach (self::LINE_AMOUNT_COLUMNS as $column) {
            $total = (int) $detail->getRawOriginal($column);

            if ($completesLine) {
                $alreadyAllocated = (int) BonLivraisonDetails::query()
                    ->where('commande_detail_id', $detail->id)
                    ->sum($column);

                $line[$column] = $total - $alreadyAllocated;
            } else {
                $line[$column] = $orderedQty > 0 ? intdiv($total * $shipQty, $orderedQty) : 0;
            }
        }

        BonLivraisonDetails::create($line);
    }

    /**
     * Mark a pending Bon de Livraison as delivered (pending → delivered).
     *
     * @throws ConversionException
     */
    public function markDelivered(BonLivraison $bonLivraison): BonLivraison
    {
        if ($bonLivraison->status !== BonLivraison::STATUS_PENDING) {
            throw new ConversionException(trans('bonlivraison.only-pending-deliverable'));
        }

        $bonLivraison->update(['status' => BonLivraison::STATUS_DELIVERED]);

        return $bonLivraison;
    }
}
