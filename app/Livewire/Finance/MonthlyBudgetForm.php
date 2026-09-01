<?php

namespace App\Livewire\Finance;

use App\Models\MonthlyBudget;
use App\Services\Finance\MonthlyBudgetService;
use Livewire\Component;

class MonthlyBudgetForm extends Component
{
    public ?int $budgetId = null;

    public int $year;

    public int $month;

    public string $starting_budget = '0';

    public ?string $note = null;

    public function mount(?MonthlyBudget $monthlyBudget = null): void
    {
        if ($monthlyBudget && $monthlyBudget->exists) {
            $this->budgetId = $monthlyBudget->id;
            $this->year = (int) $monthlyBudget->year;
            $this->month = (int) $monthlyBudget->month;
            $this->starting_budget = (string) $monthlyBudget->starting_budget;
            $this->note = $monthlyBudget->note;

            return;
        }

        $this->year = (int) now()->year;
        $this->month = (int) now()->month;
    }

    public function save(MonthlyBudgetService $budgets)
    {
        $data = $this->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'starting_budget' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:2000',
        ]);

        $duplicate = MonthlyBudget::where('year', $data['year'])
            ->where('month', $data['month'])
            ->when($this->budgetId, fn ($q) => $q->where('id', '!=', $this->budgetId))
            ->exists();

        if ($duplicate) {
            $this->addError('month', __('finance.budget_exists'));

            return null;
        }

        if ($this->budgetId) {
            $budgets->update($this->budgetId, $data);
            session()->flash('info', __('finance.budget_updated'));
        } else {
            $budget = $budgets->create($data);
            session()->flash('success', __('finance.budget_created'));

            return redirect()->route('monthly-budgets.show', $budget);
        }

        return redirect()->route('monthly-budgets.index');
    }

    public function render()
    {
        return view('livewire.finance.monthly-budget-form')
            ->layout('components.layouts.admin', ['title' => $this->budgetId ? __('finance.edit_budget') : __('finance.add_budget')]);
    }
}
