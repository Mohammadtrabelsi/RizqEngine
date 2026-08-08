<?php

namespace App\Livewire\ExpenseCategories;

use App\Models\ExpenseCategory;
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

    public function delete(int $id): void
    {
        abort_if(Gate::denies('access_expense_categories'), 403);

        $category = ExpenseCategory::findOrFail($id);

        if ($category->expenses()->exists()) {
            session()->flash('error', "Can't delete because there are expenses associated with this category.");

            return;
        }

        $category->delete();

        session()->flash('warning', trans('expense.expense-category-deleted'));
    }

    public function render()
    {
        $categories = ExpenseCategory::query()
            ->withCount('expenses')
            ->when($this->search, function ($query) {
                $query->where('category_name', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate(12);

        return view('livewire.expense-categories.expense-category-index', compact('categories'));
    }
}
