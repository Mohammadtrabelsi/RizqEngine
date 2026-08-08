<?php

namespace App\Livewire\Reports;

use App\Services\Reports\InventoryValuationService;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryValuationReport extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $category_id = '';

    public $search = '';

    public function updating($field)
    {
        if (in_array($field, ['category_id', 'search'])) {
            $this->resetPage();
        }
    }

    public function render(InventoryValuationService $service)
    {
        $totals = $service->totals($this->category_id);

        return view('livewire.reports.inventory-valuation-report', array_merge([
            'products' => $service->paginateProducts($this->category_id, $this->search, 15),
            'categories' => $service->categories(),
        ], $totals));
    }
}
