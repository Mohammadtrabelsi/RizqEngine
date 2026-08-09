<?php

namespace App\Services\Reports;

use App\Models\SaleReturn;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Data access for the sales return report.
 */
class SalesReturnReportService
{
    /**
     * Paginate sale returns within a date range, filtered by customer, return
     * status and payment status.
     */
    public function paginate(
        ?string $startDate,
        ?string $endDate,
        int|string|null $customerId,
        ?string $saleReturnStatus,
        ?string $paymentStatus,
        int $perPage
    ): LengthAwarePaginator {
        return SaleReturn::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->when($customerId, fn ($q) => $q->where('customer_id', $customerId))
            ->when($saleReturnStatus, fn ($q) => $q->where('status', $saleReturnStatus))
            ->when($paymentStatus, fn ($q) => $q->where('payment_status', $paymentStatus))
            ->orderBy('date', 'desc')
            ->paginate($perPage);
    }
}
