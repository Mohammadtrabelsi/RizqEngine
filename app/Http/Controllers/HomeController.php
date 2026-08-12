<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // --- Date range filter -----------------------------------------
        $today = Carbon::today()->toDateString();

        try {
            $from_date = request('from_date')
                ? Carbon::parse(request('from_date'))->toDateString()
                : $today;
        } catch (\Exception $e) {
            $from_date = $today;
        }

        try {
            $to_date = request('to_date')
                ? Carbon::parse(request('to_date'))->toDateString()
                : $today;
        } catch (\Exception $e) {
            $to_date = $today;
        }

        // Ensure the range is ordered correctly.
        if ($from_date > $to_date) {
            [$from_date, $to_date] = [$to_date, $from_date];
        }

        // --- KPI tiles (for the selected range) ------------------------
        $range = [$from_date.' 00:00:00', $to_date.' 23:59:59'];

        $todays_sales = Sale::completed()->whereBetween('date', $range)->sum('total_amount') / 100;
        $todays_transactions = Sale::completed()->whereBetween('date', $range)->count();
        $todays_expenses = Expense::whereBetween('date', $range)->sum('amount') / 100;

        // --- Financial summary for the selected range ------------------
        // Cost of goods sold is aggregated in a single query instead of
        // hydrating every completed sale and its line items into memory.
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

        // --- Outstanding balances (point-in-time snapshot) -------------
        $receivables = Sale::completed()->sum('due_amount') / 100;
        $payables = Purchase::completed()->sum('due_amount') / 100;

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
            ->whereBetween('date', $range)
            ->latest()
            ->take(6)
            ->get(['id', 'reference', 'customer_name', 'total_amount', 'status', 'payment_status']);

        return view('home', [
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
            'from_date' => $from_date,
            'to_date' => $to_date,
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
