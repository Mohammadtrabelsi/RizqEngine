<?php

namespace App\Livewire\Reports;

use App\Services\Reports\LowStockService;
use Livewire\Component;
use Livewire\WithPagination;

class LowStockReport extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $only_out_of_stock = false;

    public function updatingOnlyOutOfStock()
    {
        $this->resetPage();
    }

    public function render(LowStockService $service)
    {
        return view('livewire.reports.low-stock-report', [
            'products' => $service->paginate($this->only_out_of_stock, 15),
            'outOfStockCount' => $service->outOfStockCount(),
            'lowStockCount' => $service->lowStockCount(),
        ]);
    }
}
