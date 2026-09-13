<?php

namespace App\Services\Reports;

use App\Models\Expense;
use Illuminate\Support\Collection;

/**
 * Data access for the "expenses by vehicle" report. Aggregates expense
 * amounts per vehicle within an optional date range. Amounts are stored as
 * integer cents, so summed values are scaled back to major units.
 */
class ExpenseByVehicleReportService
{
    /**
     * One row per vehicle that has at least one expense in range, ordered by
     * total descending.
     *
     * @return Collection<int, object{vehicle_id: int, label: string, count: int, total_amount: float}>
     */
    public function rows(?string $startDate, ?string $endDate): Collection
    {
        return Expense::query()
            ->join('vehicles', 'expenses.vehicle_id', '=', 'vehicles.id')
            ->when($startDate, fn ($q) => $q->whereDate('expenses.date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('expenses.date', '<=', $endDate))
            ->selectRaw('vehicles.id as vehicle_id, vehicles.registration, vehicles.brand, vehicles.model, COUNT(*) as count, COALESCE(SUM(expenses.amount), 0) as total_amount')
            ->groupBy('vehicles.id', 'vehicles.registration', 'vehicles.brand', 'vehicles.model')
            ->orderByDesc('total_amount')
            ->get()
            ->map(function ($row) {
                $name = trim(($row->brand ?? '').' '.($row->model ?? ''));
                $label = $name !== '' ? $name.' — '.$row->registration : $row->registration;

                return (object) [
                    'vehicle_id' => (int) $row->vehicle_id,
                    'label' => $label,
                    'count' => (int) $row->count,
                    'total_amount' => $row->total_amount / 100,
                ];
            });
    }

    /**
     * Aggregate totals across all vehicles in range.
     *
     * @return array{count: int, total_amount: float}
     */
    public function summary(?string $startDate, ?string $endDate): array
    {
        $totals = Expense::query()
            ->whereNotNull('vehicle_id')
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
