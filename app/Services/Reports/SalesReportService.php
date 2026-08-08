<?php

namespace App\Services\Reports;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Sale;

/**
 * Data access for the sales report.
 */
class SalesReportService
{
    /**
     * Paginate sales within a date range, filtered by customer, sale status and
     * payment status.
     */
    public function paginate(
        ?string $startDate,
        ?string $endDate,
        int|string|null $customerId,
        ?string $saleStatus,
        ?string $paymentStatus,
        int $perPage
    ): LengthAwarePaginator {
        return Sale::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->when($customerId, fn ($q) => $q->where('customer_id', $customerId))
            ->when($saleStatus, fn ($q) => $q->where('status', $saleStatus))
            ->when($paymentStatus, fn ($q) => $q->where('payment_status', $paymentStatus))
            ->orderBy('date', 'desc')
            ->paginate($perPage);
    }
}
