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

    #[Url]
    public string $categoryId = '';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['categoryId', 'dateFrom', 'dateTo']);
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
            'expenses' => $expenses->paginate($this->search, [
                'category_id' => $this->categoryId,
                'date_from' => $this->dateFrom,
                'date_to' => $this->dateTo,
            ]),
            'categories' => $expenses->categories(),
        ]);
    }
}
