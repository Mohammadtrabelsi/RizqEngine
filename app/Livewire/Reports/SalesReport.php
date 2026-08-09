<?php

namespace App\Livewire\Reports;

use App\Services\Reports\SalesReportService;
use Livewire\Component;
use Livewire\WithPagination;

class SalesReport extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $customers;

    public $start_date;

    public $end_date;

    public $customer_id;

    public $sale_status;

    public $payment_status;

    protected $rules = [
        'start_date' => 'required|date|before:end_date',
        'end_date' => 'required|date|after:start_date',
    ];

    public function mount($customers)
    {
        $this->customers = $customers;
        $this->start_date = today()->subDays(30)->format('Y-m-d');
        $this->end_date = today()->format('Y-m-d');
        $this->customer_id = '';
        $this->sale_status = '';
        $this->payment_status = '';
    }

    public function render(SalesReportService $service)
    {
        return view('livewire.reports.sales-report', [
            'sales' => $service->paginate(
                $this->start_date,
                $this->end_date,
                $this->customer_id,
                $this->sale_status,
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
