<?php

namespace App\Services\Reports;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Data access for the inventory valuation report.
 */
class InventoryValuationService
{
    /**
     * Aggregate totals (quantity, cost value, retail value) across the
     * catalogue, optionally filtered by category. Values are returned in the
     * major currency unit (the raw columns are stored in cents).
     */
    public function totals(int|string|null $categoryId): array
    {
        $baseQuery = Product::query()
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId));

        $totalQuantity = (clone $baseQuery)->sum('product_quantity');
        $totalCostValue = (clone $baseQuery)->sum(DB::raw('product_quantity * product_cost')) / 100;
        $totalRetailValue = (clone $baseQuery)->sum(DB::raw('product_quantity * product_price')) / 100;

        return [
            'totalQuantity' => $totalQuantity,
            'totalCostValue' => $totalCostValue,
            'totalRetailValue' => $totalRetailValue,
            'potentialProfit' => $totalRetailValue - $totalCostValue,
        ];
    }

    /**
     * Paginate products for the report, ordered by cost value descending.
     */
    public function paginateProducts(int|string|null $categoryId, ?string $search, int $perPage): LengthAwarePaginator
    {
        return Product::with('category')
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($search, fn ($q) => $q->where('product_name', 'like', "%{$search}%")
                ->orWhere('product_code', 'like', "%{$search}%"))
            ->orderByRaw('product_quantity * product_cost DESC')
            ->paginate($perPage);
    }

    /**
     * Categories available as report filters.
     */
    public function categories(): Collection
    {
        return Category::orderBy('category_name')->get();
    }
}
