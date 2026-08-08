<?php

namespace App\Services\Reports;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\PurchaseReturn;

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
}
