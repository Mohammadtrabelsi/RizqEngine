<?php

namespace App\Livewire\ExpenseCategories;

use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ExpenseCategoryForm extends Component
{
    public ?int $categoryId = null;

    public string $category_name = '';

    public string $category_description = '';

    public function mount(?ExpenseCategory $expenseCategory = null): void
    {
        abort_if(Gate::denies('access_expense_categories'), 403);

        if ($expenseCategory && $expenseCategory->exists) {
            $this->categoryId = $expenseCategory->id;
            $this->category_name = (string) $expenseCategory->category_name;
            $this->category_description = (string) $expenseCategory->category_description;
        }
    }

    protected function rules(): array
    {
        return [
            'category_name' => [
                'required', 'string', 'max:255',
                Rule::unique('expense_categories', 'category_name')->ignore($this->categoryId),
            ],
            'category_description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function save()
    {
        abort_if(Gate::denies('access_expense_categories'), 403);

        $data = $this->validate();

        if ($this->categoryId) {
            ExpenseCategory::findOrFail($this->categoryId)->update($data);
            session()->flash('info', trans('expense.expense-category-updated'));
        } else {
            ExpenseCategory::create($data);
            session()->flash('success', trans('expense.expense-category-created'));
        }

        return redirect()->route('expense-categories.index');
    }

    public function render()
    {
        return view('livewire.expense-categories.expense-category-form');
    }
}
