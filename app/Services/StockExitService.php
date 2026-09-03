<?php

namespace App\Services;

use App\Exceptions\StockInconsistencyException;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\StockEntryDetail;
use App\Models\StockExit;
use App\Models\StockExitDetail;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Orchestrates the two-step "Sortie-Retour" workflow.
 *
 * Step 1 — {@see self::createExit()} validates availability, destocks the
 * products through {@see StockService} (so the ledger stays consistent) and
 * issues a Bon de Sortie in the "in_transit" state.
 *
 * Step 2 — {@see self::createEntry()} takes the physically confirmed return
 * quantities, restocks only what actually came back, books the difference as a
 * loss/consumption and closes the originating Bon de Sortie. It never assumes a
 * full return and refuses any return larger than what went out.
 */
class StockExitService
{
    public function __construct(
        private StockService $stockService,
        private SaleService $saleService,
    ) {}

    /**
     * Paginate stock exits (with a count of their detail lines), optionally
     * filtered by reference, destination or responsible party.
     */
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return StockExit::query()
            ->withCount('details')
            ->when($search, function ($query) use ($search) {
                $query->where('reference', 'like', '%'.$search.'%')
                    ->orWhere('destination', 'like', '%'.$search.'%')
                    ->orWhere('responsible', 'like', '%'.$search.'%');
            })
            ->latest()
            ->paginate($perPage);
    }

    public function delete(int $id): void
    {
        StockExit::findOrFail($id)->delete();
    }

    public function deleteModel(StockExit $stockExit): void
    {
        $stockExit->delete();
    }

    /**
     * Eager-load the relations a stock exit's detail view needs.
     */
    public function loadForShow(StockExit $stockExit): StockExit
    {
        return $stockExit->load(['details.product', 'entries.details.product', 'entries.sale', 'customer', 'user', 'driver', 'vehicle']);
    }

    /**
     * Eager-load the detail lines (with products) for the return-entry form.
     */
    public function loadForEntry(StockExit $stockExit): StockExit
    {
        return $stockExit->load('details.product', 'customer');
    }

    /**
     * Eager-load the "details" relation for API responses.
     */
    public function loadDetails(StockExit $stockExit): StockExit
    {
        return $stockExit->load('details');
    }

    /**
     * Eager-load a stock entry's own detail lines for API responses.
     */
    public function loadEntryDetails(StockEntry $stockEntry): StockEntry
    {
        return $stockEntry->load('details');
    }

    /**
     * Eager-load the relations a stock entry's detail view needs.
     */
    public function loadEntryForShow(StockEntry $stockEntry): StockEntry
    {
        return $stockEntry->load(['details.product', 'stockExit']);
    }

    /**
     * Create and validate a Bon de Sortie, decreasing real stock.
     *
     * @param  array<string, mixed>  $attributes  date, reason, destination, responsible, driver_id, vehicle_id, note
     * @param  array<int, array{product_id:int, quantity:int}>  $lines
     */
    public function createExit(array $attributes, array $lines): StockExit
    {
        return DB::transaction(function () use ($attributes, $lines) {
            $kind = ($attributes['kind'] ?? StockExit::KIND_STANDARD) === StockExit::KIND_CONSIGNMENT
                ? StockExit::KIND_CONSIGNMENT
                : StockExit::KIND_STANDARD;

            $stockExit = StockExit::create([
                'date' => $attributes['date'],
                'kind' => $kind,
                'customer_id' => $kind === StockExit::KIND_CONSIGNMENT ? ($attributes['customer_id'] ?? null) : null,
                'reason' => $attributes['reason'] ?? null,
                'destination' => $attributes['destination'] ?? null,
                'responsible' => $attributes['responsible'] ?? null,
                'driver_id' => $attributes['driver_id'] ?? null,
                'vehicle_id' => $attributes['vehicle_id'] ?? null,
                'note' => $attributes['note'] ?? null,
                'status' => StockExit::STATUS_IN_TRANSIT,
                'user_id' => auth()->id(),
            ]);

            foreach ($lines as $line) {
                $quantity = (int) $line['quantity'];

                if ($quantity <= 0) {
                    continue;
                }

                $product = Product::findOrFail($line['product_id']);

                // StockService throws InsufficientStockException when the
                // product does not hold enough stock, aborting the transaction.
                $this->stockService->stockOut(
                    $product,
                    $quantity,
                    note: "Bon de Sortie {$stockExit->reference}",
                    referenceType: 'StockExit',
                    referenceId: $stockExit->id,
                );

                StockExitDetail::create([
                    'stock_exit_id' => $stockExit->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'returned_quantity' => 0,
                    'lost_quantity' => 0,
                    'sold_quantity' => 0,
                    // Snapshot the selling price (cents) so régularisation bills
                    // the sold portion at the price in force when goods left.
                    'unit_price' => (int) round($product->product_price * 100),
                ]);
            }

            return $stockExit->refresh();
        });
    }

    /**
     * Create a Bon d'Entrée that closes a Bon de Sortie.
     *
     * @param  array<int, array{detail_id:int, returned:int}>  $received
     *                                                                    The physically confirmed quantity per exit line.
     *
     * @throws StockInconsistencyException when a returned quantity exceeds the
     *                                     quantity originally taken out.
     */
    public function createEntry(StockExit $stockExit, array $received, ?string $note = null, ?string $date = null): StockEntry
    {
        if ($stockExit->isClosed()) {
            throw new StockInconsistencyException(
                "Bon de Sortie {$stockExit->reference} is already closed."
            );
        }

        // Index the confirmed returns by exit-detail id for validation.
        $returnedByDetail = [];
        foreach ($received as $row) {
            $returnedByDetail[(int) $row['detail_id']] = max(0, (int) $row['returned']);
        }

        $details = $stockExit->details()->get();

        // Pre-flight: block the whole operation on any inconsistency before we
        // touch the stock — never restock more than what went out.
        foreach ($details as $detail) {
            $returned = $returnedByDetail[$detail->id] ?? 0;

            if ($returned > $detail->quantity) {
                throw new StockInconsistencyException(
                    "Incohérence de Stock: returned {$returned} unit(s) for \"{$detail->product->product_name}\" "
                    ."but only {$detail->quantity} went out on {$stockExit->reference}."
                );
            }
        }

        return DB::transaction(function () use ($stockExit, $details, $returnedByDetail, $note, $date) {
            $entry = StockEntry::create([
                'stock_exit_id' => $stockExit->id,
                'date' => $date ?? now()->toDateString(),
                'note' => $note,
                'user_id' => auth()->id(),
            ]);

            foreach ($details as $detail) {
                $returned = $returnedByDetail[$detail->id] ?? 0;
                $lost = $detail->quantity - $returned; // consumed / lost / damaged

                if ($returned > 0) {
                    $product = Product::findOrFail($detail->product_id);

                    $this->stockService->stockIn(
                        $product,
                        $returned,
                        note: "Bon d'Entrée {$entry->reference}",
                        referenceType: 'StockEntry',
                        referenceId: $entry->id,
                    );
                }

                StockEntryDetail::create([
                    'stock_entry_id' => $entry->id,
                    'product_id' => $detail->product_id,
                    'quantity_out' => $detail->quantity,
                    'quantity_returned' => $returned,
                    'quantity_lost' => $lost,
                ]);

                $detail->update([
                    'returned_quantity' => $returned,
                    'lost_quantity' => $lost,
                ]);
            }

            $stockExit->update(['status' => StockExit::STATUS_CLOSED]);

            return $entry->refresh();
        });
    }

    /**
     * Regularise a consignment (dépôt-vente) Bon de Sortie.
     *
     * Records the unsold quantity that physically came back (Bon de Retour),
     * restocks exactly that quantity and treats the rest as sold:
     *
     *     Stock Vendu = Q_init - Q_retour
     *
     * The sold portion is invoiced to the consignee at the price snapshotted
     * when the goods left — it is NOT destocked again, since the Bon de Sortie
     * already removed it from inventory at emission. The originating Bon de
     * Sortie is then closed.
     *
     * @param  array<int, array{detail_id:int, returned:int}>  $received
     *
     * @throws StockInconsistencyException when the exit is not a consignment,
     *                                     is already closed, or a returned
     *                                     quantity exceeds what went out.
     */
    public function createConsignmentReturn(StockExit $stockExit, array $received, ?string $note = null, ?string $date = null): StockEntry
    {
        if (! $stockExit->isConsignment()) {
            throw new StockInconsistencyException(
                "Bon de Sortie {$stockExit->reference} is not a consignment exit."
            );
        }

        if ($stockExit->isClosed()) {
            throw new StockInconsistencyException(
                "Bon de Sortie {$stockExit->reference} is already closed."
            );
        }

        $returnedByDetail = [];
        foreach ($received as $row) {
            $returnedByDetail[(int) $row['detail_id']] = max(0, (int) $row['returned']);
        }

        $details = $stockExit->details()->with('product')->get();

        // Pre-flight: never accept a return larger than what went out.
        foreach ($details as $detail) {
            $returned = $returnedByDetail[$detail->id] ?? 0;

            if ($returned > $detail->quantity) {
                throw new StockInconsistencyException(
                    "Incohérence de Stock: returned {$returned} unit(s) for \"{$detail->product->product_name}\" "
                    ."but only {$detail->quantity} went out on {$stockExit->reference}."
                );
            }
        }

        return DB::transaction(function () use ($stockExit, $details, $returnedByDetail, $note, $date) {
            $entry = StockEntry::create([
                'stock_exit_id' => $stockExit->id,
                'date' => $date ?? now()->toDateString(),
                'note' => $note,
                'user_id' => auth()->id(),
            ]);

            $invoiceLines = [];

            foreach ($details as $detail) {
                $returned = $returnedByDetail[$detail->id] ?? 0;
                $sold = $detail->quantity - $returned; // Stock Vendu = Q_init - Q_retour

                if ($returned > 0) {
                    $this->stockService->stockIn(
                        $detail->product,
                        $returned,
                        note: "Bon de Retour {$entry->reference}",
                        referenceType: 'StockEntry',
                        referenceId: $entry->id,
                    );
                }

                if ($sold > 0) {
                    $invoiceLines[] = [
                        'product' => $detail->product,
                        'quantity' => $sold,
                        'unit_price' => (int) ($detail->unit_price ?? round($detail->product->product_price * 100)),
                    ];
                }

                StockEntryDetail::create([
                    'stock_entry_id' => $entry->id,
                    'product_id' => $detail->product_id,
                    'quantity_out' => $detail->quantity,
                    'quantity_returned' => $returned,
                    'quantity_lost' => $sold, // regularised as sold, not lost
                ]);

                $detail->update([
                    'returned_quantity' => $returned,
                    'sold_quantity' => $sold,
                ]);
            }

            // Régularisation: invoice the sold portion to the consignee.
            if ($invoiceLines !== [] && $stockExit->customer) {
                $sale = $this->saleService->createInvoiceFromLines(
                    $stockExit->customer,
                    $invoiceLines,
                    [
                        'date' => $date ?? now()->toDateString(),
                        'note' => "Régularisation dépôt {$stockExit->reference}",
                    ],
                );

                $entry->update(['sale_id' => $sale->id]);
            }

            $stockExit->update(['status' => StockExit::STATUS_CLOSED]);

            return $entry->refresh();
        });
    }
}
