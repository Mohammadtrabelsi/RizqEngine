<?php

namespace App\Services;

use App\Enums\TransferStatus;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Owns the creation of stock transfers between warehouses. Every transfer runs
 * inside a single database transaction and moves stock atomically through the
 * {@see WarehouseStockService}, so a source and destination balance can never
 * drift apart. A transfer nets to zero against the global on-hand total, so the
 * product-level quantity is intentionally left untouched.
 */
class StockTransferService
{
    public function __construct(private readonly WarehouseStockService $warehouseStock) {}

    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return StockTransfer::query()
            ->with(['fromWarehouse', 'toWarehouse', 'lines'])
            ->when($search, function ($query) use ($search) {
                $query->where('reference', 'like', '%'.$search.'%');
            })
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Create a completed transfer and move the stock.
     *
     * @param  array<int, array{product_id:int, quantity:int}>  $lines
     *
     * @throws \InvalidArgumentException when source and destination match or lines are empty.
     * @throws \App\Exceptions\InsufficientStockException when the source lacks stock.
     */
    public function create(Warehouse $from, Warehouse $to, array $lines, ?string $date = null, ?string $note = null): StockTransfer
    {
        if ($from->id === $to->id) {
            throw new \InvalidArgumentException('The source and destination warehouses must be different.');
        }

        $lines = array_values(array_filter($lines, fn ($line) => (int) ($line['quantity'] ?? 0) > 0));

        if ($lines === []) {
            throw new \InvalidArgumentException('A transfer must contain at least one line with a positive quantity.');
        }

        return DB::transaction(function () use ($from, $to, $lines, $date, $note) {
            $transfer = new StockTransfer([
                'reference' => make_reference_id('TR', (int) StockTransfer::max('id') + 1),
                'from_warehouse_id' => $from->id,
                'to_warehouse_id' => $to->id,
                'date' => $date ?? now()->toDateString(),
                'status' => TransferStatus::Completed,
                'note' => $note,
            ]);
            $transfer->save();

            foreach ($lines as $line) {
                $product = Product::findOrFail($line['product_id']);
                $quantity = (int) $line['quantity'];

                // Move stock: decrement source (guards against shortage), then
                // credit destination. Both operations lock their pivot row.
                $this->warehouseStock->decrement($product, $from, $quantity);
                $this->warehouseStock->increment($product, $to, $quantity);

                $transfer->lines()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);
            }

            return $transfer->load('lines');
        });
    }
}
