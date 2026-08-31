<?php

namespace App\Services\Reports;

use App\Models\PurchaseReturn;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Data access for the purchases return report.
 */
class PurchasesReturnReportService
{
    /**
     * Paginate purchase returns within a date range, filtered by supplier,
     * return status and payment status.
     */
    public function paginate(
        ?string $startDate,
        ?string $endDate,
        int|string|null $supplierId,
        ?string $purchaseReturnStatus,
        ?string $paymentStatus,
        int $perPage
    ): LengthAwarePaginator {
        return PurchaseReturn::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->when($supplierId, fn ($q) => $q->where('supplier_id', $supplierId))
            ->when($purchaseReturnStatus, fn ($q) => $q->where('status', $purchaseReturnStatus))
            ->when($paymentStatus, fn ($q) => $q->where('payment_status', $paymentStatus))
            ->orderBy('date', 'desc')
            ->paginate($perPage);
    }

    /**
     * Aggregate totals for the purchase returns matching the given filters.
     * Amounts are stored as integer cents, so results are scaled back to major
     * units.
     *
     * @return array{count: int, total_amount: float, paid_amount: float, due_amount: float}
     */
    public function summary(
        ?string $startDate,
        ?string $endDate,
        int|string|null $supplierId,
        ?string $purchaseReturnStatus,
        ?string $paymentStatus
    ): array {
        $totals = PurchaseReturn::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->when($supplierId, fn ($q) => $q->where('supplier_id', $supplierId))
            ->when($purchaseReturnStatus, fn ($q) => $q->where('status', $purchaseReturnStatus))
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
