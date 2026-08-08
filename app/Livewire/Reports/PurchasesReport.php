<?php

namespace App\Livewire\Reports;

use App\Services\Reports\PurchasesReportService;
use Livewire\Component;
use Livewire\WithPagination;

class PurchasesReport extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $suppliers;

    public $start_date;

    public $end_date;

    public $supplier_id;

    public $purchase_status;

    public $payment_status;

    protected $rules = [
        'start_date' => 'required|date|before:end_date',
        'end_date' => 'required|date|after:start_date',
    ];

    public function mount($suppliers)
    {
        $this->suppliers = $suppliers;
        $this->start_date = today()->subDays(30)->format('Y-m-d');
        $this->end_date = today()->format('Y-m-d');
        $this->supplier_id = '';
        $this->purchase_status = '';
        $this->payment_status = '';
    }

    public function render(PurchasesReportService $service)
    {
        return view('livewire.reports.purchases-report', [
            'purchases' => $service->paginate(
                $this->start_date,
                $this->end_date,
                $this->supplier_id,
                $this->purchase_status,
                $this->payment_status,
                10
            ),
        ]);
    }

    public function generateReport()
    {
        $this->validate();
    }
}
