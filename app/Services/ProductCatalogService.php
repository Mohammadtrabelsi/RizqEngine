<?php

namespace App\Services;

use App\Enums\StockStatus;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
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
     * Paginate the product index applying the full set of catalogue filters.
     *
     * @param  array{
     *     search?: string,
     *     stock_status?: StockStatus|null,
     *     category_id?: int|string|null,
     *     supplier_id?: int|string|null,
     *     min_price?: string|null,
     *     max_price?: string|null,
     *     expiry?: string|null,
     * }  $filters
     */
    public function paginateIndex(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $search = $filters['search'] ?? '';
        $stockStatus = $filters['stock_status'] ?? null;
        $categoryId = $filters['category_id'] ?? '';
        $supplierId = $filters['supplier_id'] ?? '';
        $minPrice = $filters['min_price'] ?? '';
        $maxPrice = $filters['max_price'] ?? '';
        $expiry = $filters['expiry'] ?? '';

        return Product::query()
            ->with('category', 'supplier')
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('product_name', 'like', $term)
                        ->orWhere('product_code', 'like', $term);
                });
            })
            ->when($stockStatus, function ($query) use ($stockStatus) {
                $query->stockStatus($stockStatus);
            })
            ->when($categoryId !== '', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($supplierId !== '', function ($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->when(is_numeric($minPrice), function ($query) use ($minPrice) {
                // Prices are persisted in cents (see Product::$product_price accessor).
                $query->where('product_price', '>=', (int) round(((float) $minPrice) * 100));
            })
            ->when(is_numeric($maxPrice), function ($query) use ($maxPrice) {
                $query->where('product_price', '<=', (int) round(((float) $maxPrice) * 100));
            })
            ->when($expiry === 'expired', fn ($query) => $query->expired())
            ->when($expiry === 'expiring_soon', fn ($query) => $query->expiringSoon())
            ->when($expiry === 'not_expired', function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('expiry_date')
                        ->orWhereDate('expiry_date', '>', now()->toDateString());
                });
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Number of currently expired products that still hold stock.
     */
    public function expiredInStockCount(): int
    {
        return Product::query()->expired()->where('product_quantity', '>', 0)->count();
    }

    /**
     * Force every expired product that still has stock to zero quantity,
     * moving them to the "out of stock" status.
     *
     * @return int Number of products affected.
     */
    public function markExpiredOutOfStock(): int
    {
        return Product::query()
            ->expired()
            ->where('product_quantity', '>', 0)
            ->update(['product_quantity' => 0]);
    }

    public function delete(int $id): void
    {
        Product::findOrFail($id)->delete();
    }

    /**
     * Paginate products, optionally filtered by category and/or stock status.
     */
    public function paginateByCategory(
        int|string|null $categoryId,
        ?int $perPage = null,
        ?StockStatus $stockStatus = null
    ): LengthAwarePaginator {
        $perPage = $perPage ?: 9;

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
