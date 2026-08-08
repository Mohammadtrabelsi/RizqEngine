<?php

namespace App\Livewire\Reports;

use App\Models\Product;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class StockMovementReport extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $start_date;

    public $end_date;

    public $product_id = '';

    public $type = '';

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
        $this->resetPage();
    }

    public function render()
    {
        $movements = StockMovement::with(['product', 'user'])
            ->whereDate('created_at', '>=', $this->start_date)
            ->whereDate('created_at', '<=', $this->end_date)
            ->when($this->product_id, fn ($q) => $q->where('product_id', $this->product_id))
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->latest()
            ->paginate(20);

        return view('livewire.reports.stock-movement-report', [
            'movements' => $movements,
            'products' => Product::orderBy('product_name')->get(['id', 'product_name']),
        ]);
    }
}
