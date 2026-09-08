<?php

namespace App\Livewire\Reports;

use App\Services\Reports\PeriodicSummaryService;
use Livewire\Component;

/**
 * Periodic "bilan" report: sales, purchases, outings and expenses aggregated
 * per day, week or month, with a balance column and grand totals.
 */
class PeriodicSummaryReport extends Component
{
    public $start_date;

    public $end_date;

    public $grouping;

    protected $rules = [
        'start_date' => 'required|date|before_or_equal:end_date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'grouping' => 'required|in:day,week,month',
    ];

    public function mount()
    {
        $this->start_date = today()->startOfMonth()->format('Y-m-d');
        $this->end_date = today()->format('Y-m-d');
        $this->grouping = 'day';
    }

    public function render(PeriodicSummaryService $service)
    {
        $summary = $service->summary($this->start_date, $this->end_date, $this->grouping);

        return view('livewire.reports.periodic-summary-report', [
            'rows' => $summary['rows'],
            'totals' => $summary['totals'],
        ]);
    }

    public function generateReport()
    {
        $this->validate();
    }
}
