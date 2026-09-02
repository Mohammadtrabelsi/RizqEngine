<?php

namespace App\Services;

use App\Http\Controllers\HomeController;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnPayment;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\SaleReturn;
use App\Models\SaleReturnPayment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Owns every query behind the classic home dashboard and its AJAX charts,
 * keeping the reporting SQL out of {@see HomeController}.
 *
 * Monetary columns are stored as integer cents and converted to major units
 * before leaving this service.
 */
class HomeReportService
{
    /**
     * Assemble the full home-dashboard view payload for the given range.
     *
     * @param  string  $fromDate  Y-m-d
     * @param  string  $toDate  Y-m-d
     * @return array<string, mixed>
     */
    public function dashboardData(string $fromDate, string $toDate): array
    {
        $range = [$fromDate.' 00:00:00', $toDate.' 23:59:59'];

        $todays_sales = Sale::completed()->whereBetween('date', $range)->sum('total_amount') / 100;
        $todays_transactions = Sale::completed()->whereBetween('date', $range)->count();
        $todays_expenses = Expense::whereBetween('date', $range)->sum('amount') / 100;

        $gross_sales = Sale::completed()->whereBetween('date', $range)->sum('total_amount');
        $sale_returns = SaleReturn::completed()->whereBetween('date', $range)->sum('total_amount');
        $purchase_returns = PurchaseReturn::completed()->whereBetween('date', $range)->sum('total_amount');

        $cost_of_goods = DB::table('sale_details')
            ->join('sales', 'sales.id', '=', 'sale_details.sale_id')
            ->join('products', 'products.id', '=', 'sale_details.product_id')
            ->where('sales.status', 'Completed')
            ->whereBetween('sales.date', $range)
            ->sum(DB::raw('sale_details.quantity * products.product_cost'));

        $revenue = ($gross_sales - $sale_returns) / 100;
        $cost_of_goods = $cost_of_goods / 100;
        $profit = $revenue - $cost_of_goods;

        $receivables = Sale::completed()->sum('due_amount') / 100;
        $payables = Purchase::completed()->sum('due_amount') / 100;

        $low_stock_products = Product::select('id', 'product_name', 'product_code', 'product_quantity', 'product_stock_alert')
            ->whereColumn('product_quantity', '<=', 'product_stock_alert')
            ->orderBy('product_quantity')
            ->get();

        [$week_bars, $week_max] = $this->weeklySalesBars();

        $recent_sales = Sale::withCount('saleDetails')
            ->whereBetween('date', $range)
            ->latest()
            ->take(6)
            ->get(['id', 'reference', 'customer_name', 'total_amount', 'status', 'payment_status']);

        return [
            'revenue' => $revenue,
            'cost_of_goods' => $cost_of_goods,
            'sale_returns' => $sale_returns / 100,
            'purchase_returns' => $purchase_returns / 100,
            'profit' => $profit,
            'receivables' => $receivables,
            'payables' => $payables,
            'todays_sales' => $todays_sales,
            'todays_transactions' => $todays_transactions,
            'todays_expenses' => $todays_expenses,
            'low_stock_products' => $low_stock_products,
            'week_bars' => $week_bars,
            'week_max' => $week_max,
            'recent_sales' => $recent_sales,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'kpis' => $this->kpiTiles($todays_sales, $todays_transactions, $low_stock_products->count(), $todays_expenses),
            'summary' => $this->summaryTiles($revenue, $cost_of_goods, $profit, $receivables, $payables),
        ];
    }

    /**
     * The four headline KPI tiles for the dashboard.
     *
     * @return list<array{label: string, value: string, meta: string, icon: string}>
     */
    private function kpiTiles(float|int $sales, int $transactions, int $lowStock, float|int $expenses): array
    {
        return [
            ['label' => __('general.sales-today'), 'value' => format_currency($sales), 'meta' => __('general.completed-sales-today'), 'icon' => 'bi-bag-check'],
            ['label' => __('general.transactions'), 'value' => $transactions, 'meta' => __('general.orders-today'), 'icon' => 'bi-receipt'],
            ['label' => __('general.low-stock-items'), 'value' => $lowStock, 'meta' => __('general.needs-reorder'), 'icon' => 'bi-exclamation-triangle'],
            ['label' => __('general.todays-expenses'), 'value' => format_currency($expenses), 'meta' => __('general.logged-today'), 'icon' => 'bi-wallet2'],
        ];
    }

    /**
     * The five financial-summary tiles for the dashboard.
     *
     * @return list<array{label: string, value: string, meta: string, icon: string, tone: string}>
     */
    private function summaryTiles(float|int $revenue, float|int $cogs, float|int $profit, float|int $receivables, float|int $payables): array
    {
        return [
            ['label' => __('general.revenue'), 'value' => format_currency($revenue), 'meta' => __('general.net-of-returns'), 'icon' => 'bi-cash-coin', 'tone' => 'text-indigo-600'],
            ['label' => __('general.cost-of-goods'), 'value' => format_currency($cogs), 'meta' => __('general.for-selected-period'), 'icon' => 'bi-box-seam', 'tone' => 'text-slate-500'],
            ['label' => __('general.gross-profit'), 'value' => format_currency($profit), 'meta' => __('general.revenue-minus-cost'), 'icon' => 'bi-graph-up', 'tone' => $profit >= 0 ? 'text-emerald-600' : 'text-rose-600'],
            ['label' => __('general.receivables'), 'value' => format_currency($receivables), 'meta' => __('general.due-from-customers'), 'icon' => 'bi-arrow-down-left-circle', 'tone' => 'text-emerald-600'],
            ['label' => __('general.payables'), 'value' => format_currency($payables), 'meta' => __('general.due-to-suppliers'), 'icon' => 'bi-arrow-up-right-circle', 'tone' => 'text-rose-600'],
        ];
    }

    /**
     * Completed-sales totals bucketed over the last seven days.
     *
     * @return array{0: Collection, 1: float|int}
     */
    private function weeklySalesBars(): array
    {
        $week = collect();
        foreach (range(-6, 0) as $i) {
            $date = Carbon::today()->addDays($i);
            $week->put($date->toDateString(), [
                'label' => $date->format('D'),
                'amount' => 0.0,
            ]);
        }

        $weekly_sales = Sale::completed()
            ->where('date', '>=', Carbon::today()->subDays(6)->toDateString())
            ->groupBy(DB::raw('DATE(date)'))
            ->get([
                DB::raw('DATE(date) as day'),
                DB::raw('SUM(total_amount) as amount'),
            ]);

        foreach ($weekly_sales as $row) {
            if ($week->has($row['day'])) {
                $entry = $week->get($row['day']);
                $entry['amount'] = $row['amount'] / 100;
                $week->put($row['day'], $entry);
            }
        }

        $week_max = max($week->max('amount'), 1);

        // Pre-compute each bar's height percentage (min 4%) for the chart so
        // the view carries no arithmetic.
        $week_bars = $week->map(function (array $bar) use ($week_max) {
            $bar['pct'] = max(4, round(($bar['amount'] / $week_max) * 100));

            return $bar;
        })->values();

        return [$week_bars, $week_max];
    }

    /**
     * Current-month sales/purchases/expenses totals for the summary chart.
     *
     * @return array{sales: float|int, purchases: float|int, expenses: float|int}
     */
    public function currentMonthTotals(): array
    {
        return [
            'sales' => Sale::where('status', 'Completed')->whereMonth('date', date('m'))
                ->whereYear('date', date('Y'))->sum('total_amount') / 100,
            'purchases' => Purchase::where('status', 'Completed')->whereMonth('date', date('m'))
                ->whereYear('date', date('Y'))->sum('total_amount') / 100,
            'expenses' => Expense::whereMonth('date', date('m'))
                ->whereYear('date', date('Y'))->sum('amount') / 100,
        ];
    }

    /**
     * Monthly payments received vs. sent over the last twelve months.
     *
     * @return array{payment_sent: array<int, float|int>, payment_received: array<int, float|int>, months: array<int, string>}
     */
    public function paymentChartData(): array
    {
        $dates = collect();
        foreach (range(-11, 0) as $i) {
            $date = Carbon::now()->addMonths($i)->format('m-Y');
            $dates->put($date, 0);
        }

        $date_range = Carbon::today()->subYear()->format('Y-m-d');

        $sale_payments = $this->monthlyTotals(SalePayment::query(), $date_range);
        $sale_return_payments = $this->monthlyTotals(SaleReturnPayment::query(), $date_range);
        $purchase_payments = $this->monthlyTotals(PurchasePayment::query(), $date_range);
        $purchase_return_payments = $this->monthlyTotals(PurchaseReturnPayment::query(), $date_range);
        $expenses = $this->monthlyTotals(Expense::query(), $date_range);

        $payment_received = array_merge_numeric_values($sale_payments, $purchase_return_payments);
        $payment_sent = array_merge_numeric_values($purchase_payments, $sale_return_payments, $expenses);

        $dates_received = $dates->merge($payment_received);
        $dates_sent = $dates->merge($payment_sent);

        $received_payments = [];
        $sent_payments = [];
        $months = [];

        foreach ($dates_received as $key => $value) {
            $received_payments[] = $value;
            $months[] = $key;
        }

        foreach ($dates_sent as $value) {
            $sent_payments[] = $value;
        }

        return [
            'payment_sent' => $sent_payments,
            'payment_received' => $received_payments,
            'months' => $months,
        ];
    }

    /**
     * Sum a payment/expense query grouped by "%m-%Y" month, keyed by month.
     */
    private function monthlyTotals($query, string $sinceDate): Collection
    {
        return $query->where('date', '>=', $sinceDate)
            ->select([
                DB::raw("DATE_FORMAT(date, '%m-%Y') as month"),
                DB::raw('SUM(amount) as amount'),
            ])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('amount', 'month');
    }

    /**
     * Completed-sales totals over the last seven days, keyed for the chart.
     *
     * @return array{data: array<int, float|int>, days: array<int, string>}
     */
    public function salesChartData(): array
    {
        return $this->dailyCompletedTotals(Sale::query());
    }

    /**
     * Completed-purchases totals over the last seven days, keyed for the chart.
     *
     * @return array{data: array<int, float|int>, days: array<int, string>}
     */
    public function purchasesChartData(): array
    {
        return $this->dailyCompletedTotals(Purchase::query());
    }

    /**
     * Completed totals bucketed by day over the last seven days.
     *
     * @return array{data: array<int, float|int>, days: array<int, string>}
     */
    private function dailyCompletedTotals($query): array
    {
        $dates = collect();
        foreach (range(-6, 0) as $i) {
            $date = Carbon::now()->addDays($i)->format('d-m-y');
            $dates->put($date, 0);
        }

        $date_range = Carbon::today()->subDays(6);

        $totals = $query->where('status', 'Completed')
            ->where('date', '>=', $date_range)
            ->groupBy(DB::raw("DATE_FORMAT(date,'%d-%m-%y')"))
            ->orderBy('date')
            ->get([
                DB::raw("DATE_FORMAT(date,'%d-%m-%y') as date"),
                DB::raw('SUM(total_amount) AS count'),
            ])
            ->pluck('count', 'date');

        $dates = $dates->merge($totals);

        $data = [];
        $days = [];
        foreach ($dates as $key => $value) {
            $data[] = $value / 100;
            $days[] = $key;
        }

        return ['data' => $data, 'days' => $days];
    }
}
