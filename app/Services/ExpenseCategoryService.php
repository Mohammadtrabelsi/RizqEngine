<?php

namespace App\Services;

use App\Models\ExpenseCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Owns all persistence for expense categories.
 */
class ExpenseCategoryService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return ExpenseCategory::query()
            ->withCount('expenses')
            ->when($search, function ($query) use ($search) {
                $query->where('category_name', 'like', '%'.$search.'%');
            })
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): ExpenseCategory
    {
        return ExpenseCategory::create($data);
    }

    public function update(int $id, array $data): ExpenseCategory
    {
        $category = ExpenseCategory::findOrFail($id);
        $category->update($data);

        return $category;
    }

    /**
     * Delete a category, refusing when expenses are still attached.
     *
     * @return bool True when deleted, false when blocked by related expenses.
     */
    public function delete(int $id): bool
    {
        $category = ExpenseCategory::findOrFail($id);

        if ($category->expenses()->exists()) {
            return false;
        }

        $category->delete();

        return true;
    }
}
