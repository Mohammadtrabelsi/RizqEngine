<?php

namespace App\Livewire\Reports;

use App\Services\Reports\VatReturnService;
use Livewire\Component;

/**
 * VAT return (déclaration de TVA) report: collected vs deductible VAT for a
 * period, broken down by rate, with the net VAT due or credit.
 */
class VatReturnReport extends Component
{
    public $start_date;

    public $end_date;

    protected $rules = [
        'start_date' => 'required|date|before_or_equal:end_date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ];

    public function mount(): void
    {
        // Default to the current calendar month, the Tunisian VAT period.
        $this->start_date = today()->startOfMonth()->format('Y-m-d');
        $this->end_date = today()->endOfMonth()->format('Y-m-d');
    }

    public function render(VatReturnService $service)
    {
        return view('livewire.reports.vat-return-report', [
            'report' => $service->build($this->start_date, $this->end_date),
        ]);
    }

    public function generateReport(): void
    {
        $this->validate();
    }
}
