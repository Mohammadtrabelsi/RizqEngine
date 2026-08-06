<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Expense\Entities\Expense;
use Modules\Product\Entities\Product;
use Modules\Purchase\Entities\Purchase;
use Modules\Purchase\Entities\PurchasePayment;
use Modules\PurchasesReturn\Entities\PurchaseReturn;
use Modules\PurchasesReturn\Entities\PurchaseReturnPayment;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SalePayment;
use Modules\SalesReturn\Entities\SaleReturn;
use Modules\SalesReturn\Entities\SaleReturnPayment;

class HomeController extends Controller
{
    public function index()
    {
        $sales = Sale::completed()->sum('total_amount');
        $sale_returns = SaleReturn::completed()->sum('total_amount');
        $purchase_returns = PurchaseReturn::completed()->sum('total_amount');
        $product_costs = 0;

        foreach (Sale::completed()->with('saleDetails')->get() as $sale) {
            foreach ($sale->saleDetails as $saleDetail) {
                if (! is_null($saleDetail->product)) {
                    $product_costs += $saleDetail->product->product_cost * $saleDetail->quantity;
                }
            }
        }

        $revenue = ($sales - $sale_returns) / 100;
        $profit = $revenue - $product_costs;

        $today = Carbon::today()->toDateString();

        // --- KPI tiles -------------------------------------------------
        $todays_sales = Sale::completed()->whereDate('date', $today)->sum('total_amount') / 100;
        $todays_transactions = Sale::completed()->whereDate('date', $today)->count();
        $todays_expenses = Expense::whereDate('date', $today)->sum('amount') / 100;

        $low_stock_products = Product::select('id', 'product_name', 'product_code', 'product_quantity', 'product_stock_alert')
            ->whereColumn('product_quantity', '<=', 'product_stock_alert')
            ->orderBy('product_quantity')
            ->get();

        // --- Weekly sales bars (last 7 days) ---------------------------
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
        $week_bars = $week->values();
        $week_max = max($week_bars->max('amount'), 1);

        // --- Recent transactions --------------------------------------
        $recent_sales = Sale::withCount('saleDetails')
            ->latest()
            ->take(6)
            ->get(['id', 'reference', 'customer_name', 'total_amount', 'status', 'payment_status']);

        return view('home', [
            'revenue' => $revenue,
            'sale_returns' => $sale_returns / 100,
            'purchase_returns' => $purchase_returns / 100,
            'profit' => $profit,
            'todays_sales' => $todays_sales,
            'todays_transactions' => $todays_transactions,
            'todays_expenses' => $todays_expenses,
            'low_stock_products' => $low_stock_products,
            'week_bars' => $week_bars,
            'week_max' => $week_max,
            'recent_sales' => $recent_sales,
        ]);
    }

    public function currentMonthChart()
    {
        abort_if(! request()->ajax(), 404);

        $currentMonthSales = Sale::where('status', 'Completed')->whereMonth('date', date('m'))
            ->whereYear('date', date('Y'))
            ->sum('total_amount') / 100;
        $currentMonthPurchases = Purchase::where('status', 'Completed')->whereMonth('date', date('m'))
            ->whereYear('date', date('Y'))
            ->sum('total_amount') / 100;
        $currentMonthExpenses = Expense::whereMonth('date', date('m'))
            ->whereYear('date', date('Y'))
            ->sum('amount') / 100;

        return response()->json([
            'sales' => $currentMonthSales,
            'purchases' => $currentMonthPurchases,
            'expenses' => $currentMonthExpenses,
        ]);
    }

    public function salesPurchasesChart()
    {
        abort_if(! request()->ajax(), 404);

        $sales = $this->salesChartData();
        $purchases = $this->purchasesChartData();

        return response()->json(['sales' => $sales, 'purchases' => $purchases]);
    }

    public function paymentChart()
    {
        abort_if(! request()->ajax(), 404);

        $dates = collect();
        foreach (range(-11, 0) as $i) {
            $date = Carbon::now()->addMonths($i)->format('m-Y');
            $dates->put($date, 0);
        }

        $date_range = Carbon::today()->subYear()->format('Y-m-d');

        $sale_payments = SalePayment::where('date', '>=', $date_range)
            ->select([
                DB::raw("DATE_FORMAT(date, '%m-%Y') as month"),
                DB::raw('SUM(amount) as amount'),
            ])
            ->groupBy('month')->orderBy('month')
            ->get()->pluck('amount', 'month');

        $sale_return_payments = SaleReturnPayment::where('date', '>=', $date_range)
            ->select([
                DB::raw("DATE_FORMAT(date, '%m-%Y') as month"),
                DB::raw('SUM(amount) as amount'),
            ])
            ->groupBy('month')->orderBy('month')
            ->get()->pluck('amount', 'month');

        $purchase_payments = PurchasePayment::where('date', '>=', $date_range)
            ->select([
                DB::raw("DATE_FORMAT(date, '%m-%Y') as month"),
                DB::raw('SUM(amount) as amount'),
            ])
            ->groupBy('month')->orderBy('month')
            ->get()->pluck('amount', 'month');

        $purchase_return_payments = PurchaseReturnPayment::where('date', '>=', $date_range)
            ->select([
                DB::raw("DATE_FORMAT(date, '%m-%Y') as month"),
                DB::raw('SUM(amount) as amount'),
            ])
            ->groupBy('month')->orderBy('month')
            ->get()->pluck('amount', 'month');

        $expenses = Expense::where('date', '>=', $date_range)
            ->select([
                DB::raw("DATE_FORMAT(date, '%m-%Y') as month"),
                DB::raw('SUM(amount) as amount'),
            ])
            ->groupBy('month')->orderBy('month')
            ->get()->pluck('amount', 'month');

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

        foreach ($dates_sent as $key => $value) {
            $sent_payments[] = $value;
        }

        return response()->json([
            'payment_sent' => $sent_payments,
            'payment_received' => $received_payments,
            'months' => $months,
        ]);
    }

    public function salesChartData()
    {
        $dates = collect();
        foreach (range(-6, 0) as $i) {
            $date = Carbon::now()->addDays($i)->format('d-m-y');
            $dates->put($date, 0);
        }

        $date_range = Carbon::today()->subDays(6);

        $sales = Sale::where('status', 'Completed')
            ->where('date', '>=', $date_range)
            ->groupBy(DB::raw("DATE_FORMAT(date,'%d-%m-%y')"))
            ->orderBy('date')
            ->get([
                DB::raw("DATE_FORMAT(date,'%d-%m-%y') as date"),
                DB::raw('SUM(total_amount) AS count'),
            ])
            ->pluck('count', 'date');

        $dates = $dates->merge($sales);

        $data = [];
        $days = [];
        foreach ($dates as $key => $value) {
            $data[] = $value / 100;
            $days[] = $key;
        }

        return response()->json(['data' => $data, 'days' => $days]);
    }

    public function purchasesChartData()
    {
        $dates = collect();
        foreach (range(-6, 0) as $i) {
            $date = Carbon::now()->addDays($i)->format('d-m-y');
            $dates->put($date, 0);
        }

        $date_range = Carbon::today()->subDays(6);

        $purchases = Purchase::where('status', 'Completed')
            ->where('date', '>=', $date_range)
            ->groupBy(DB::raw("DATE_FORMAT(date,'%d-%m-%y')"))
            ->orderBy('date')
            ->get([
                DB::raw("DATE_FORMAT(date,'%d-%m-%y') as date"),
                DB::raw('SUM(total_amount) AS count'),
            ])
            ->pluck('count', 'date');

        $dates = $dates->merge($purchases);

        $data = [];
        $days = [];
        foreach ($dates as $key => $value) {
            $data[] = $value / 100;
            $days[] = $key;
        }

        return response()->json(['data' => $data, 'days' => $days]);

    }
}
