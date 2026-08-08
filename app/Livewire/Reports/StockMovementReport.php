<?php

namespace App\Livewire\Reports;

use App\Services\Reports\StockMovementService;
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

    public function render(StockMovementService $service)
    {
        return view('livewire.reports.stock-movement-report', [
            'movements' => $service->paginate(
                $this->start_date,
                $this->end_date,
                $this->product_id,
                $this->type,
                20
            ),
            'products' => $service->products(),
        ]);
    }
}
