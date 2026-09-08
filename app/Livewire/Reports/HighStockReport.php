<?php

namespace App\Livewire\Reports;

use App\Services\Reports\HighStockService;
use Livewire\Component;
use Livewire\WithPagination;

class HighStockReport extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render(HighStockService $service)
    {
        return view('livewire.reports.high-stock-report', [
            'products' => $service->paginate(15),
            'highStockCount' => $service->highStockCount(),
        ]);
    }
}
