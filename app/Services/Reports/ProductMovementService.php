<?php

namespace App\Services\Reports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

/**
 * Data access for the product movement report.
 */
class ProductMovementService
{
    /**
     * Rank products by units sold across completed sales in the given window.
     *
     * @param  string  $direction  'slow' for least-sold first, otherwise best sellers first.
     */
    public function ranked(string $startDate, string $endDate, string $direction, int $limit): Collection
    {
        $soldPerProduct = DB::table('sale_details')
            ->join('sales', 'sales.id', '=', 'sale_details.sale_id')
            ->where('sales.status', 'Completed')
            ->whereDate('sales.date', '>=', $startDate)
            ->whereDate('sales.date', '<=', $endDate)
            ->whereNotNull('sale_details.product_id')
            ->groupBy('sale_details.product_id')
            ->select('sale_details.product_id', DB::raw('SUM(sale_details.quantity) as units_sold'))
            ->pluck('units_sold', 'product_id');

        $products = Product::all()->map(function ($product) use ($soldPerProduct) {
            $product->units_sold = (int) ($soldPerProduct[$product->id] ?? 0);

            return $product;
        });

        $ranked = $direction === 'slow'
            ? $products->sortBy('units_sold')
            : $products->sortByDesc('units_sold');

        return $ranked->take($limit)->values();
    }
}
