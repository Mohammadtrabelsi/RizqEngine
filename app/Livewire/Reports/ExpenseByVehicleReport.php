<?php

namespace App\Livewire\Reports;

use App\Services\Reports\ExpenseByVehicleReportService;
use Livewire\Component;

class ExpenseByVehicleReport extends Component
{
    public $start_date;

    public $end_date;

    protected $rules = [
        'start_date' => 'required|date|before_or_equal:end_date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ];

    public function mount(): void
    {
        $this->start_date = today()->subDays(30)->format('Y-m-d');
        $this->end_date = today()->format('Y-m-d');
    }

    public function generateReport(): void
    {
        $this->validate();
    }

    public function render(ExpenseByVehicleReportService $service)
    {
        return view('livewire.reports.expense-by-vehicle-report', [
            'rows' => $service->rows($this->start_date, $this->end_date),
            'summary' => $service->summary($this->start_date, $this->end_date),
        ]);
    }
}
