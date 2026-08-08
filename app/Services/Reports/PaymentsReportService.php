<?php

namespace App\Services\Reports;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Purchase\Entities\PurchasePayment;
use Modules\PurchasesReturn\Entities\PurchaseReturnPayment;
use Modules\Sale\Entities\SalePayment;
use Modules\SalesReturn\Entities\SaleReturnPayment;

/**
 * Data access for the payments report.
 */
class PaymentsReportService
{
    /**
     * Paginate payments of the given type within an optional date range and
     * payment method. Returns an empty collection for an unknown type.
     */
    public function paginate(
        ?string $type,
        ?string $startDate,
        ?string $endDate,
        ?string $paymentMethod,
        int $perPage
    ): LengthAwarePaginator|Collection {
        $query = $this->queryFor($type);

        if (! $query) {
            return collect();
        }

        return $query->orderBy('date', 'desc')
            ->when($startDate, fn ($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('date', '<=', $endDate))
            ->when($paymentMethod, fn ($q) => $q->where('payment_method', $paymentMethod))
            ->paginate($perPage);
    }

    /**
     * Base query builder for the requested payment type, eager loading its
     * parent document. Returns null when the type is not recognised.
     */
    protected function queryFor(?string $type)
    {
        return match ($type) {
            'sale' => SalePayment::query()->with('sale'),
            'sale_return' => SaleReturnPayment::query()->with('saleReturn'),
            'purchase' => PurchasePayment::query()->with('purchase'),
            'purchase_return' => PurchaseReturnPayment::query()->with('purchaseReturn'),
            default => null,
        };
    }
}
