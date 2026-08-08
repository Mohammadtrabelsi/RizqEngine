<?php

namespace App\Livewire\Reports;

use App\Services\Reports\ProductMovementService;
use Livewire\Component;

/**
 * Ranks products by how much stock left the business (completed sales) over a
 * period, so buyers can see fast-moving vs slow-moving lines at a glance.
 */
class ProductMovementReport extends Component
{
    public $start_date;

    public $end_date;

    public $direction = 'fast'; // fast = best sellers first, slow = least sold first

    public $limit = 10;

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
    }

    public function render(ProductMovementService $service)
    {
        return view('livewire.reports.product-movement-report', [
            'products' => $service->ranked(
                $this->start_date,
                $this->end_date,
                $this->direction,
                (int) $this->limit
            ),
        ]);
    }
}
