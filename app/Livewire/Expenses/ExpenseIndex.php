<?php

namespace App\Livewire\Expenses;

use App\Services\ExpenseService;
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

    public function delete(int $id, ExpenseService $expenses): void
    {
        abort_if(Gate::denies('delete_expenses'), 403);

        $expenses->delete($id);

        session()->flash('warning', trans('expense.expense-deleted'));
    }

    public function render(ExpenseService $expenses)
    {
        return view('livewire.expenses.expense-index', [
            'expenses' => $expenses->paginate($this->search),
        ]);
    }
}
