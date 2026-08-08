<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Product\Entities\Product;

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
        return Product::findOrFail($id);
    }

    /**
     * Search products by name or code, limited to a maximum result count.
     */
    public function search(string $term, int $limit): Collection
    {
        return Product::where('product_name', 'like', '%'.$term.'%')
            ->orWhere('product_code', 'like', '%'.$term.'%')
            ->take($limit)
            ->get();
    }

    /**
     * Paginate products, optionally filtered by category.
     */
    public function paginateByCategory(int|string|null $categoryId, int $perPage): LengthAwarePaginator
    {
        return Product::when($categoryId, function ($query) use ($categoryId) {
            return $query->where('category_id', $categoryId);
        })->paginate($perPage);
    }
}
