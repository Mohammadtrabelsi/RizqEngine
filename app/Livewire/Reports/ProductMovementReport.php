<?php

namespace App\Livewire\Reports;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Modules\Product\Entities\Product;

/**
 * Ranks products by how much stock left the business (completed sales) over a
 * period, so buyers can see fast-moving vs slow-moving lines at a glance.
 */
class ProductMovementReport extends Component
{
    public $start_date;

    public $end_date;

    public $direction = 'fast'; // fast = best sellers first, slow = least sold first

    public $limit = 10;

    protected $rules = [
        'start_date' => 'required|date|before_or_equal:end_date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ];

    public function mount()
    {
        $this->start_date = today()->subDays(30)->format('Y-m-d');
        $this->end_date = today()->format('Y-m-d');
    }

    public function generateReport()
    {
        $this->validate();
    }

    public function render()
    {
        // Units sold per product across completed sales in the window.
        $soldPerProduct = DB::table('sale_details')
            ->join('sales', 'sales.id', '=', 'sale_details.sale_id')
            ->where('sales.status', 'Completed')
            ->whereDate('sales.date', '>=', $this->start_date)
            ->whereDate('sales.date', '<=', $this->end_date)
            ->whereNotNull('sale_details.product_id')
            ->groupBy('sale_details.product_id')
            ->select('sale_details.product_id', DB::raw('SUM(sale_details.quantity) as units_sold'))
            ->pluck('units_sold', 'product_id');

        $products = Product::all()->map(function ($product) use ($soldPerProduct) {
            $product->units_sold = (int) ($soldPerProduct[$product->id] ?? 0);

            return $product;
        });

        $ranked = $this->direction === 'slow'
            ? $products->sortBy('units_sold')
            : $products->sortByDesc('units_sold');

        return view('livewire.reports.product-movement-report', [
            'products' => $ranked->take((int) $this->limit)->values(),
        ]);
    }
}
