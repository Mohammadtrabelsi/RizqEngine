<?php

namespace App\Services\Finance;

use App\Models\MonthlyBudget;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Owns persistence for the monthly financial envelopes so Livewire components
 * never touch the Eloquent model directly.
 */
class MonthlyBudgetService
{
    /**
     * @param  array{year?: int|string, month?: int|string}  $filters
     */
    public function paginate(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return MonthlyBudget::query()
            ->when($filters['year'] ?? null, fn ($query, $year) => $query->where('year', $year))
            ->when($filters['month'] ?? null, fn ($query, $month) => $query->where('month', $month))
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate($perPage);
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): MonthlyBudget
    {
        return MonthlyBudget::create($data);
    }

    /** @param array<string, mixed> $data */
    public function update(int $id, array $data): MonthlyBudget
    {
        $budget = MonthlyBudget::findOrFail($id);
        $budget->update($data);

        return $budget;
    }

    public function delete(int $id): void
    {
        MonthlyBudget::findOrFail($id)->delete();
    }

    /**
     * Distinct years present in the budgets table, newest first — for the
     * index page's year filter.
     *
     * @return array<int, int>
     */
    public function years(): array
    {
        return MonthlyBudget::query()
            ->orderByDesc('year')
            ->distinct()
            ->pluck('year')
            ->all();
    }
}
