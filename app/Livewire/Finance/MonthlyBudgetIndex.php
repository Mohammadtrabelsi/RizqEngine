<?php

namespace App\Livewire\Finance;

use App\Services\Finance\MonthlyBudgetService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class MonthlyBudgetIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url]
    public string $year = '';

    #[Url]
    public string $month = '';

    public function updatingYear(): void
    {
        $this->resetPage();
    }

    public function updatingMonth(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['year', 'month']);
        $this->resetPage();
    }

    public function delete(int $id, MonthlyBudgetService $budgets): void
    {
        $budgets->delete($id);

        session()->flash('warning', __('finance.budget_deleted'));
    }

    public function render(MonthlyBudgetService $budgets)
    {
        return view('livewire.finance.monthly-budget-index', [
            'budgets' => $budgets->paginate([
                'year' => $this->year,
                'month' => $this->month,
            ]),
            'years' => $budgets->years(),
        ])->layout('components.layouts.admin', ['title' => __('finance.monthly_budgets')]);
    }
}
