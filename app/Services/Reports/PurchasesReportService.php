<?php

namespace App\Services\Reports;

use App\Models\Purchase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Data access for the purchases report.
 */
class PurchasesReportService
{
    /**
     * Paginate purchases within a date range, filtered by supplier, purchase
     * status and payment status.
     */
    public function paginate(
        ?string $startDate,
        ?string $endDate,
        int|string|null $supplierId,
        ?string $purchaseStatus,
        ?string $paymentStatus,
        int $perPage
    ): LengthAwarePaginator {
        return Purchase::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->when($supplierId, fn ($q) => $q->where('supplier_id', $supplierId))
            ->when($purchaseStatus, fn ($q) => $q->where('status', $purchaseStatus))
            ->when($paymentStatus, fn ($q) => $q->where('payment_status', $paymentStatus))
            ->orderBy('date', 'desc')
            ->paginate($perPage);
    }

    /**
     * Aggregate totals for the purchases matching the given filters. Amounts are
     * stored as integer cents, so results are scaled back to major units.
     *
     * @return array{count: int, total_amount: float, paid_amount: float, due_amount: float}
     */
    public function summary(
        ?string $startDate,
        ?string $endDate,
        int|string|null $supplierId,
        ?string $purchaseStatus,
        ?string $paymentStatus
    ): array {
        $totals = Purchase::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->when($supplierId, fn ($q) => $q->where('supplier_id', $supplierId))
            ->when($purchaseStatus, fn ($q) => $q->where('status', $purchaseStatus))
            ->when($paymentStatus, fn ($q) => $q->where('payment_status', $paymentStatus))
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total_amount, COALESCE(SUM(paid_amount), 0) as paid_amount, COALESCE(SUM(due_amount), 0) as due_amount')
            ->first();

        return [
            'count' => (int) $totals->count,
            'total_amount' => $totals->total_amount / 100,
            'paid_amount' => $totals->paid_amount / 100,
            'due_amount' => $totals->due_amount / 100,
        ];
    }
}
