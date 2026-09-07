<?php

namespace App\Services\Reports;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Data access for the high stock (overstock) report.
 */
class HighStockService
{
    /**
     * Paginate products whose on-hand quantity has exceeded their configured
     * high-stock alert threshold.
     */
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return Product::with('category')
            ->whereNotNull('product_stock_alert_max')
            ->whereColumn('product_quantity', '>', 'product_stock_alert_max')
            ->orderByDesc('product_quantity')
            ->paginate($perPage);
    }

    /**
     * Count of products above their high-stock alert threshold.
     */
    public function highStockCount(): int
    {
        return Product::whereNotNull('product_stock_alert_max')
            ->whereColumn('product_quantity', '>', 'product_stock_alert_max')
            ->count();
    }
}
