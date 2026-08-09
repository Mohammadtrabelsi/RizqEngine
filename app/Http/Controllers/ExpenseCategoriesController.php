<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Services\ExpenseCategoryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class ExpenseCategoriesController extends Controller
{
    public function __construct(private readonly ExpenseCategoryService $categories) {}

    public function index()
    {
        abort_if(Gate::denies('access_expense_categories'), 403);

        return view('expense.categories.index');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('access_expense_categories'), 403);

        $data = $request->validate([
            'category_name' => 'required|string|max:255|unique:expense_categories,category_name',
            'category_description' => 'nullable|string|max:1000',
        ]);

        $this->categories->create($data);

        session()->flash('success', trans('expense.expense-category-created'));

        return redirect()->route('expense-categories.index');
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        abort_if(Gate::denies('access_expense_categories'), 403);

        return view('expense.categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        abort_if(Gate::denies('access_expense_categories'), 403);

        $data = $request->validate([
            'category_name' => 'required|string|max:255|unique:expense_categories,category_name,'.$expenseCategory->id,
            'category_description' => 'nullable|string|max:1000',
        ]);

        $this->categories->update($expenseCategory->id, $data);

        session()->flash('info', trans('expense.expense-category-updated'));

        return redirect()->route('expense-categories.index');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        abort_if(Gate::denies('access_expense_categories'), 403);

        if (! $this->categories->delete($expenseCategory->id)) {
            return back()->withErrors('Can\'t delete beacuse there are expenses associated with this category.');
        }

        session()->flash('warning', trans('expense.expense-category-deleted'));

        return redirect()->route('expense-categories.index');
    }
}
