<?php

namespace App\Services\Reports;

use App\Models\PurchasePayment;
use App\Models\PurchaseReturnPayment;
use App\Models\SalePayment;
use App\Models\SaleReturnPayment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
     * Aggregate totals for the payments matching the given filters. Amounts are
     * stored as integer cents, so the summed value is scaled back to major
     * units. Returns zeroed totals for an unknown type.
     *
     * @return array{count: int, total_amount: float}
     */
    public function summary(
        ?string $type,
        ?string $startDate,
        ?string $endDate,
        ?string $paymentMethod
    ): array {
        $query = $this->queryFor($type);

        if (! $query) {
            return ['count' => 0, 'total_amount' => 0.0];
        }

        $totals = $query
            ->when($startDate, fn ($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('date', '<=', $endDate))
            ->when($paymentMethod, fn ($q) => $q->where('payment_method', $paymentMethod))
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(amount), 0) as total_amount')
            ->first();

        return [
            'count' => (int) $totals->count,
            'total_amount' => $totals->total_amount / 100,
        ];
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
