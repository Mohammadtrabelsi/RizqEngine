<?php

namespace App\Http\Controllers;

use App\Services\HomeReportService;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function __construct(private readonly HomeReportService $reports) {}

    public function index()
    {
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

        return view('home', $this->reports->dashboardData($from_date, $to_date));
    }

    public function currentMonthChart()
    {
        abort_if(! request()->ajax(), 404);

        return response()->json($this->reports->currentMonthTotals());
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

        return response()->json($this->reports->paymentChartData());
    }

    public function salesChartData()
    {
        return response()->json($this->reports->salesChartData());
    }

    public function purchasesChartData()
    {
        return response()->json($this->reports->purchasesChartData());
    }
}
