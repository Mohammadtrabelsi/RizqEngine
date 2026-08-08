<?php

namespace App\Services\Reports;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Purchase\Entities\Purchase;

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
}
