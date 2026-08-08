<?php

namespace App\Livewire\Reports;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryValuationReport extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $category_id = '';

    public $search = '';

    public function updating($field)
    {
        if (in_array($field, ['category_id', 'search'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        // Totals are computed from raw (cents) columns so the whole catalogue is
        // summed in a single query rather than hydrating every model.
        $totalsQuery = Product::query()
            ->when($this->category_id, fn ($q) => $q->where('category_id', $this->category_id));

        $totalQuantity = (clone $totalsQuery)->sum('product_quantity');
        $totalCostValue = (clone $totalsQuery)->sum(\DB::raw('product_quantity * product_cost')) / 100;
        $totalRetailValue = (clone $totalsQuery)->sum(\DB::raw('product_quantity * product_price')) / 100;

        $products = Product::with('category')
            ->when($this->category_id, fn ($q) => $q->where('category_id', $this->category_id))
            ->when($this->search, fn ($q) => $q->where('product_name', 'like', "%{$this->search}%")
                ->orWhere('product_code', 'like', "%{$this->search}%"))
            ->orderByRaw('product_quantity * product_cost DESC')
            ->paginate(15);

        return view('livewire.reports.inventory-valuation-report', [
            'products' => $products,
            'categories' => Category::orderBy('category_name')->get(),
            'totalQuantity' => $totalQuantity,
            'totalCostValue' => $totalCostValue,
            'totalRetailValue' => $totalRetailValue,
            'potentialProfit' => $totalRetailValue - $totalCostValue,
        ]);
    }
}
