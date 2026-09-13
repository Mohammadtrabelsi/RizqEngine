<?php

namespace App\Services\Reports;

use App\Models\Expense;
use Illuminate\Support\Collection;

/**
 * Data access for the "expenses by driver" report. Aggregates expense
 * amounts per driver within an optional date range. Amounts are stored as
 * integer cents, so summed values are scaled back to major units.
 */
class ExpenseByDriverReportService
{
    /**
     * One row per driver that has at least one expense in range, ordered by
     * total descending.
     *
     * @return Collection<int, object{driver_id: int, name: string, phone: ?string, count: int, total_amount: float}>
     */
    public function rows(?string $startDate, ?string $endDate): Collection
    {
        return Expense::query()
            ->join('drivers', 'expenses.driver_id', '=', 'drivers.id')
            ->when($startDate, fn ($q) => $q->whereDate('expenses.date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('expenses.date', '<=', $endDate))
            ->selectRaw('drivers.id as driver_id, drivers.name, drivers.phone, COUNT(*) as count, COALESCE(SUM(expenses.amount), 0) as total_amount')
            ->groupBy('drivers.id', 'drivers.name', 'drivers.phone')
            ->orderByDesc('total_amount')
            ->get()
            ->map(fn ($row) => (object) [
                'driver_id' => (int) $row->driver_id,
                'name' => $row->name,
                'phone' => $row->phone,
                'count' => (int) $row->count,
                'total_amount' => $row->total_amount / 100,
            ]);
    }

    /**
     * Aggregate totals across all drivers in range.
     *
     * @return array{count: int, total_amount: float}
     */
    public function summary(?string $startDate, ?string $endDate): array
    {
        $totals = Expense::query()
            ->whereNotNull('driver_id')
            ->when($startDate, fn ($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('date', '<=', $endDate))
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(amount), 0) as total_amount')
            ->first();

        return [
            'count' => (int) $totals->count,
            'total_amount' => $totals->total_amount / 100,
        ];
    }
}
