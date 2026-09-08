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
     * Paginate bons de livraison, optionally filtered by reference or customer name.
     */
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return BonLivraison::query()
            ->with('commande')
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('reference', 'like', $term)
                    ->orWhere('customer_name', 'like', $term);
            })
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
        $bonLivraison->delete();
    }

    /**
     * @var string[]
     */
    private const HEADER_COLUMNS = [
        'tax_percentage',
        'tax_amount',
        'discount_percentage',
        'discount_amount',
        'shipping_amount',
        'total_amount',
        'note',
    ];

    /**
     * @var string[]
     */
    private const LINE_COLUMNS = [
        'product_id',
        'product_name',
        'product_code',
        'quantity',
        'price',
        'unit_price',
        'sub_total',
        'product_discount_amount',
        'product_discount_type',
        'product_tax_amount',
    ];

    /**
     * Transform a Commande into a Bon de Livraison (delivery note).
     *
     * @throws ConversionException when the Commande has already produced a
     *                             delivery note.
     */
    public function createFromCommande(Commande $commande): BonLivraison
    {
        return DB::transaction(function () use ($commande) {
            // Serialize concurrent conversions of the same Commande.
            $commande = Commande::lockForUpdate()->findOrFail($commande->id);

            if ($commande->bonLivraison()->exists()) {
                throw new ConversionException(
                    trans('bonlivraison.already-converted', ['reference' => $commande->reference])
                );
            }

            $bonLivraison = new BonLivraison;
            $bonLivraison->date = now()->format('Y-m-d');
            $bonLivraison->commande_id = $commande->id;
            $bonLivraison->customer_id = $commande->customer_id;
            $bonLivraison->customer_name = $commande->customer_name;
            $bonLivraison->status = BonLivraison::STATUS_PENDING;

            foreach (self::HEADER_COLUMNS as $column) {
                $bonLivraison->{$column} = $commande->getRawOriginal($column);
            }

            $bonLivraison->save();

            foreach ($commande->commandeDetails as $detail) {
                $line = ['bon_livraison_id' => $bonLivraison->id];

                foreach (self::LINE_COLUMNS as $column) {
                    $line[$column] = $detail->getRawOriginal($column);
                }

                BonLivraisonDetails::create($line);
            }

            return $bonLivraison->refresh();
        });
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
