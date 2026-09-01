<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Owns all persistence and querying for expenses, keeping Eloquent access
 * out of the Livewire layer.
 */
class ExpenseService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Expense::query()
            ->with('category')
            ->when($search, function ($query) use ($search) {
                $query->where('reference', 'like', '%'.$search.'%');
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * All expense categories, for populating the form select.
     *
     * @return Collection<int, ExpenseCategory>
     */
    public function categories(): Collection
    {
        return ExpenseCategory::all();
    }

    public function create(array $data): Expense
    {
        return Expense::create($data);
    }

    public function update(int $id, array $data): Expense
    {
        $expense = Expense::findOrFail($id);
        $expense->update($data);

        return $expense;
    }

    public function delete(int $id): void
    {
        Expense::findOrFail($id)->delete();
    }
}
