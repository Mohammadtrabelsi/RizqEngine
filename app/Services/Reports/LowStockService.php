<?php

namespace App\Services\Reports;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Product;

/**
 * Data access for the low stock report.
 */
class LowStockService
{
    /**
     * Paginate products that are low on stock. When $onlyOutOfStock is true only
     * products with no remaining quantity are returned; otherwise products whose
     * on-hand quantity has reached or dropped below their configured alert level.
     */
    public function paginate(bool $onlyOutOfStock, int $perPage): LengthAwarePaginator
    {
        return Product::with('category')
            ->when(
                $onlyOutOfStock,
                fn ($q) => $q->where('product_quantity', '<=', 0),
                fn ($q) => $q->whereColumn('product_quantity', '<=', 'product_stock_alert')
            )
            ->orderBy('product_quantity')
            ->paginate($perPage);
    }

    /**
     * Count of products that are completely out of stock.
     */
    public function outOfStockCount(): int
    {
        return Product::where('product_quantity', '<=', 0)->count();
    }

    /**
     * Count of products at or below their alert threshold.
     */
    public function lowStockCount(): int
    {
        return Product::whereColumn('product_quantity', '<=', 'product_stock_alert')->count();
    }
}
