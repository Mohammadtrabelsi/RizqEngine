<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

/**
 * Manages the per-warehouse distribution of a product's stock, held on the
 * `product_warehouse` pivot. The global `products.product_quantity` stays the
 * authoritative total; this service only records *where* that stock sits so
 * transfers and per-site reporting are possible without disturbing existing
 * modules.
 */
class WarehouseStockService
{
    /**
     * Quantity of a product currently held in a warehouse.
     */
    public function quantityFor(Product $product, Warehouse $warehouse): int
    {
        return (int) DB::table('product_warehouse')
            ->where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->value('quantity');
    }

    /**
     * Add quantity to a product's stock in a warehouse.
     */
    public function increment(Product $product, Warehouse $warehouse, int $quantity, ?int $locationId = null): void
    {
        $this->guardPositive($quantity);

        DB::transaction(function () use ($product, $warehouse, $quantity, $locationId) {
            $row = $this->lockedRow($product->id, $warehouse->id);

            if ($row === null) {
                DB::table('product_warehouse')->insert([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'location_id' => $locationId,
                    'quantity' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return;
            }

            DB::table('product_warehouse')
                ->where('id', $row->id)
                ->update([
                    'quantity' => $row->quantity + $quantity,
                    'location_id' => $locationId ?? $row->location_id,
                    'updated_at' => now(),
                ]);
        });
    }

    /**
     * Remove quantity from a product's stock in a warehouse.
     *
     * @throws InsufficientStockException when the warehouse holds too little.
     */
    public function decrement(Product $product, Warehouse $warehouse, int $quantity): void
    {
        $this->guardPositive($quantity);

        DB::transaction(function () use ($product, $warehouse, $quantity) {
            $row = $this->lockedRow($product->id, $warehouse->id);
            $available = $row->quantity ?? 0;

            if ($available < $quantity) {
                throw new InsufficientStockException(
                    "Warehouse \"{$warehouse->name}\" holds only {$available} unit(s) of ".
                    "\"{$product->product_name}\"; cannot remove {$quantity}."
                );
            }

            DB::table('product_warehouse')
                ->where('id', $row->id)
                ->update([
                    'quantity' => $available - $quantity,
                    'updated_at' => now(),
                ]);
        });
    }

    /**
     * Fetch the pivot row for a product/warehouse pair with a pessimistic lock
     * so concurrent transfers cannot race on the same balance.
     */
    protected function lockedRow(int $productId, int $warehouseId): ?object
    {
        return DB::table('product_warehouse')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->lockForUpdate()
            ->first();
    }

    protected function guardPositive(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Warehouse stock quantity must be greater than zero.');
        }
    }
}
