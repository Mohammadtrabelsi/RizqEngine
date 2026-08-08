<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ExpenseIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        abort_if(Gate::denies('delete_expenses'), 403);

        Expense::findOrFail($id)->delete();

        session()->flash('warning', trans('expense.expense-deleted'));
    }

    public function render()
    {
        $expenses = Expense::query()
            ->with('category')
            ->when($this->search, function ($query) {
                $query->where('reference', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate(12);

        return view('livewire.expenses.expense-index', compact('expenses'));
    }
}
