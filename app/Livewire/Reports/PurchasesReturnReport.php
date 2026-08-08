<?php

namespace App\Livewire\Reports;

use App\Services\Reports\PurchasesReturnReportService;
use Livewire\Component;
use Livewire\WithPagination;

class PurchasesReturnReport extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $suppliers;

    public $start_date;

    public $end_date;

    public $supplier_id;

    public $purchase_return_status;

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
        $this->purchase_return_status = '';
        $this->payment_status = '';
    }

    public function render(PurchasesReturnReportService $service)
    {
        return view('livewire.reports.purchases-return-report', [
            'purchase_returns' => $service->paginate(
                $this->start_date,
                $this->end_date,
                $this->supplier_id,
                $this->purchase_return_status,
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
