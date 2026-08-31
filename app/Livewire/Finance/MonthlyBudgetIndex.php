<?php

namespace App\Livewire\Finance;

use App\Services\Finance\MonthlyBudgetService;
use Livewire\Component;
use Livewire\WithPagination;

class MonthlyBudgetIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function delete(int $id, MonthlyBudgetService $budgets): void
    {
        $budgets->delete($id);

        session()->flash('warning', __('finance.budget_deleted'));
    }

    public function render(MonthlyBudgetService $budgets)
    {
        return view('livewire.finance.monthly-budget-index', [
            'budgets' => $budgets->paginate(),
        ])->layout('components.layouts.admin', ['title' => __('finance.monthly_budgets')]);
    }
}
