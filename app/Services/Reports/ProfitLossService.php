<?php

namespace App\Services\Reports;

use App\Models\Expense;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnPayment;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\SaleReturn;
use App\Models\SaleReturnPayment;

/**
 * Data access and aggregation for the profit & loss report.
 *
 * Monetary amounts are returned in the major currency unit (columns are stored
 * in cents and divided by 100 here).
 */
class ProfitLossService
{
    /**
     * Compute every figure the profit & loss report displays for the given
     * (optional) date range.
     */
    public function summary(?string $startDate, ?string $endDate): array
    {
        $salesAmount = $this->completedTotalAmount(Sale::completed(), $startDate, $endDate);
        $saleReturnsAmount = $this->completedTotalAmount(SaleReturn::completed(), $startDate, $endDate);
        $purchasesAmount = $this->completedTotalAmount(Purchase::completed(), $startDate, $endDate);
        $purchaseReturnsAmount = $this->completedTotalAmount(PurchaseReturn::completed(), $startDate, $endDate);
        $expensesAmount = $this->sumAmount(Expense::query(), $startDate, $endDate);

        $paymentsReceived = $this->sumAmount(SalePayment::query(), $startDate, $endDate)
            + $this->sumAmount(PurchaseReturnPayment::query(), $startDate, $endDate);
        $paymentsSent = $this->sumAmount(PurchasePayment::query(), $startDate, $endDate)
            + $this->sumAmount(SaleReturnPayment::query(), $startDate, $endDate)
            + $expensesAmount;

        return [
            'total_sales' => $this->completedCount(Sale::completed(), $startDate, $endDate),
            'sales_amount' => $salesAmount,
            'total_purchases' => $this->completedCount(Purchase::completed(), $startDate, $endDate),
            'purchases_amount' => $purchasesAmount,
            'total_sale_returns' => $this->completedCount(SaleReturn::completed(), $startDate, $endDate),
            'sale_returns_amount' => $saleReturnsAmount,
            'total_purchase_returns' => $this->completedCount(PurchaseReturn::completed(), $startDate, $endDate),
            'purchase_returns_amount' => $purchaseReturnsAmount,
            'expenses_amount' => $expensesAmount,
            'profit_amount' => $this->calculateProfit($startDate, $endDate, $salesAmount, $saleReturnsAmount),
            'payments_received_amount' => $paymentsReceived,
            'payments_sent_amount' => $paymentsSent,
            'payments_net_amount' => $paymentsReceived - $paymentsSent,
        ];
    }

    /**
     * Count records in a query restricted to the date range.
     */
    protected function completedCount($query, ?string $startDate, ?string $endDate): int
    {
        return $this->applyDateRange($query, $startDate, $endDate)->count();
    }

    /**
     * Sum a query's total_amount column (in the major currency unit) over the range.
     */
    protected function completedTotalAmount($query, ?string $startDate, ?string $endDate): float
    {
        return $this->applyDateRange($query, $startDate, $endDate)->sum('total_amount') / 100;
    }

    /**
     * Sum a query's amount column (in the major currency unit) over the range.
     */
    protected function sumAmount($query, ?string $startDate, ?string $endDate): float
    {
        return $this->applyDateRange($query, $startDate, $endDate)->sum('amount') / 100;
    }

    /**
     * Profit = (sales - sale returns) revenue minus the cost of goods sold on
     * completed sales in the range.
     */
    protected function calculateProfit(?string $startDate, ?string $endDate, float $salesAmount, float $saleReturnsAmount): float
    {
        $revenue = $salesAmount - $saleReturnsAmount;

        $sales = $this->applyDateRange(Sale::completed(), $startDate, $endDate)
            ->with('saleDetails')
            ->get();

        $productCosts = 0;
        foreach ($sales as $sale) {
            foreach ($sale->saleDetails as $saleDetail) {
                $productCosts += $saleDetail->product->product_cost;
            }
        }

        return $revenue - $productCosts;
    }

    /**
     * Apply the optional start/end date filters to a query builder.
     */
    protected function applyDateRange($query, ?string $startDate, ?string $endDate)
    {
        return $query
            ->when($startDate, fn ($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('date', '<=', $endDate));
    }
}
