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
     * Receive purchased/returned stock into a warehouse.
     *
     * When no warehouse is given the default warehouse is used (falling back to
     * the first active one). This is best-effort: if the installation has no
     * warehouse at all it is a no-op, because the global product quantity stays
     * the authoritative total.
     */
    public function receive(Product $product, int $quantity, ?Warehouse $warehouse = null): void
    {
        $this->guardPositive($quantity);

        $target = $warehouse ?? $this->defaultWarehouse();

        if ($target === null) {
            return;
        }

        $this->increment($product, $target, $quantity);
    }

    /**
     * Release sold stock from the warehouses, drawing from the default
     * warehouse first and overflowing to the others (highest balance first)
     * until the quantity is satisfied.
     *
     * Best-effort by design: the global `product_quantity` (guarded elsewhere)
     * remains authoritative, so a shortfall in the per-warehouse pivot — for
     * instance on legacy stock that predates warehouse tracking — never blocks
     * a sale. Returns the quantity actually drawn from warehouses.
     */
    public function release(Product $product, int $quantity, ?Warehouse $warehouse = null): int
    {
        $this->guardPositive($quantity);

        return DB::transaction(function () use ($product, $quantity, $warehouse) {
            $remaining = $quantity;

            $order = $warehouse !== null ? [$warehouse->id] : $this->allocationOrder($product);

            foreach ($order as $warehouseId) {
                if ($remaining <= 0) {
                    break;
                }

                $row = DB::table('product_warehouse')
                    ->where('product_id', $product->id)
                    ->where('warehouse_id', $warehouseId)
                    ->lockForUpdate()
                    ->first();

                $available = (int) ($row->quantity ?? 0);

                if ($available <= 0) {
                    continue;
                }

                $take = min($available, $remaining);

                DB::table('product_warehouse')
                    ->where('id', $row->id)
                    ->update([
                        'quantity' => $available - $take,
                        'updated_at' => now(),
                    ]);

                $remaining -= $take;
            }

            return $quantity - $remaining;
        });
    }

    /**
     * The default warehouse, or the first active one when none is flagged.
     */
    protected function defaultWarehouse(): ?Warehouse
    {
        return Warehouse::default() ?? Warehouse::query()->active()->orderBy('id')->first();
    }

    /**
     * Warehouse ids to draw a sale from, in priority order: the default
     * warehouse first, then the remaining warehouses holding this product by
     * descending balance.
     *
     * @return array<int, int>
     */
    protected function allocationOrder(Product $product): array
    {
        $balances = DB::table('product_warehouse')
            ->where('product_id', $product->id)
            ->where('quantity', '>', 0)
            ->orderByDesc('quantity')
            ->pluck('quantity', 'warehouse_id');

        $defaultId = optional(Warehouse::default())->id;

        $order = [];

        if ($defaultId !== null && $balances->has($defaultId)) {
            $order[] = $defaultId;
        }

        foreach ($balances->keys() as $warehouseId) {
            if ($warehouseId !== $defaultId) {
                $order[] = (int) $warehouseId;
            }
        }

        return $order;
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
