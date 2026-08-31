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
    public function paginate(int $perPage = 12): LengthAwarePaginator
    {
        return MonthlyBudget::query()
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
}
