<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

class LowStockReport extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $only_out_of_stock = false;

    public function updatingOnlyOutOfStock()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::with('category')
            ->when(
                $this->only_out_of_stock,
                fn ($q) => $q->where('product_quantity', '<=', 0),
                // Alert threshold: on-hand quantity has reached or dropped below
                // the per-product alert level configured on the product.
                fn ($q) => $q->whereColumn('product_quantity', '<=', 'product_stock_alert')
            )
            ->orderBy('product_quantity');

        $outOfStockCount = Product::where('product_quantity', '<=', 0)->count();
        $lowStockCount = Product::whereColumn('product_quantity', '<=', 'product_stock_alert')->count();

        return view('livewire.reports.low-stock-report', [
            'products' => $query->paginate(15),
            'outOfStockCount' => $outOfStockCount,
            'lowStockCount' => $lowStockCount,
        ]);
    }
}
