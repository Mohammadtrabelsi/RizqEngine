<?php

namespace App\Services;

use App\Enums\StockStatus;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Read access to the product catalogue for Livewire components.
 *
 * Keeps Eloquent queries out of the Livewire layer: components ask this
 * service for products instead of building queries against the model.
 */
class ProductCatalogService
{
    /**
     * Find a single product by its primary key or fail.
     */
    public function findOrFail(int|string $id): Product
    {
        return Product::with('category')->findOrFail($id);
    }

    /**
     * Search products by name or code, limited to a maximum result count.
     *
     * Optional filters narrow the results:
     *  - $categoryId  restricts to a single category.
     *  - $inStockOnly hides products that are out of stock.
     *
     * @param  array{category?: int|string|null, in_stock_only?: bool}  $filters
     */
    public function search(string $term, int $limit, array $filters = []): Collection
    {
        $categoryId = $filters['category'] ?? null;
        $inStockOnly = $filters['in_stock_only'] ?? false;

        return Product::with('category')
            ->where(function ($query) use ($term) {
                $query->where('product_name', 'like', '%'.$term.'%')
                    ->orWhere('product_code', 'like', '%'.$term.'%');
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($inStockOnly, function ($query) {
                $query->where('product_quantity', '>', 0);
            })
            ->take($limit)
            ->get();
    }

    /**
     * Paginate products, optionally filtered by category and/or stock status.
     */
    public function paginateByCategory(
        int|string|null $categoryId,
        int $perPage,
        ?StockStatus $stockStatus = null
    ): LengthAwarePaginator {
        return Product::with('category')
            ->when($categoryId, function ($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($stockStatus, function ($query) use ($stockStatus) {
                return $query->stockStatus($stockStatus);
            })
            ->paginate($perPage);
    }
}
