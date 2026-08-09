<?php

namespace App\Livewire\ExpenseCategories;

use App\Services\ExpenseCategoryService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ExpenseCategoryIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, ExpenseCategoryService $categories): void
    {
        abort_if(Gate::denies('access_expense_categories'), 403);

        if (! $categories->delete($id)) {
            session()->flash('error', "Can't delete because there are expenses associated with this category.");

            return;
        }

        session()->flash('warning', trans('expense.expense-category-deleted'));
    }

    public function render(ExpenseCategoryService $categories)
    {
        return view('livewire.expense-categories.expense-category-index', [
            'categories' => $categories->paginate($this->search),
        ]);
    }
}
