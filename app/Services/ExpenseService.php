<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Owns all persistence and querying for expenses, keeping Eloquent access
 * out of the Livewire layer.
 */
class ExpenseService
{
    /**
     * @param  array{category_id?: string, date_from?: string, date_to?: string}  $filters
     */
    public function paginate(?string $search = null, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return Expense::query()
            ->with(['category', 'driver', 'vehicle'])
            ->when($search, function ($query) use ($search) {
                $query->where('reference', 'like', '%'.$search.'%');
            })
            ->when($filters['category_id'] ?? null, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('date', '<=', $date))
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

    /**
     * All drivers, for populating the form select.
     *
     * @return Collection<int, Driver>
     */
    public function drivers(): Collection
    {
        return Driver::orderBy('name')->get();
    }

    /**
     * All vehicles, for populating the form select.
     *
     * @return Collection<int, Vehicle>
     */
    public function vehicles(): Collection
    {
        return Vehicle::orderBy('registration')->get();
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
