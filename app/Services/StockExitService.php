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
    public function __construct(private StockService $stockService) {}

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

    /**
     * Create and validate a Bon de Sortie, decreasing real stock.
     *
     * @param  array<string, mixed>  $attributes  date, reason, destination, responsible, note
     * @param  array<int, array{product_id:int, quantity:int}>  $lines
     */
    public function createExit(array $attributes, array $lines): StockExit
    {
        return DB::transaction(function () use ($attributes, $lines) {
            $stockExit = StockExit::create([
                'date' => $attributes['date'],
                'reason' => $attributes['reason'] ?? null,
                'destination' => $attributes['destination'] ?? null,
                'responsible' => $attributes['responsible'] ?? null,
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
}
