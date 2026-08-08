<?php

namespace App\Livewire\Reports;

use App\Services\Reports\PaymentsReportService;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentsReport extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $start_date;

    public $end_date;

    public $payments;

    public $payment_method;

    protected $rules = [
        'start_date' => 'required|date|before:end_date',
        'end_date' => 'required|date|after:start_date',
        'payments' => 'required|string',
    ];

    public function mount()
    {
        $this->start_date = today()->subDays(30)->format('Y-m-d');
        $this->end_date = today()->format('Y-m-d');
        $this->payments = '';
    }

    public function render(PaymentsReportService $service)
    {
        return view('livewire.reports.payments-report', [
            'information' => $service->paginate(
                $this->payments,
                $this->start_date,
                $this->end_date,
                $this->payment_method,
                10
            ),
        ]);
    }

    public function generateReport()
    {
        $this->validate();
    }

    public function updatedPayments($value)
    {
        $this->resetPage();
    }
}
