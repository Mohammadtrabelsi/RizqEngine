<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Owns all persistence for product categories.
 */
class CategoryService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Category::query()
            ->withCount('products')
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('category_name', 'like', $term)
                    ->orWhere('category_code', 'like', $term);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Suggest the next sequential category code (CA_01, CA_02, ...).
     */
    public function nextCode(): string
    {
        return 'CA_'.str_pad((string) (Category::max('id') + 1), 2, '0', STR_PAD_LEFT);
    }

    public function findOrFail(int|string $id): Category
    {
        return Category::findOrFail($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(int $id, array $data): Category
    {
        $category = Category::findOrFail($id);
        $category->update($data);

        return $category;
    }

    /**
     * Delete a category, refusing when products are still attached.
     *
     * @return bool True when deleted, false when blocked by related products.
     */
    public function delete(int $id): bool
    {
        $category = Category::findOrFail($id);

        if ($category->products()->exists()) {
            return false;
        }

        $category->delete();

        return true;
    }
}
