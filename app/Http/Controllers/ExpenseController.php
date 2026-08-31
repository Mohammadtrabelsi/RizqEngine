<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class ExpenseController extends Controller
{
    public function __construct(private readonly ExpenseService $expenses) {}

    public function index()
    {
        abort_if(Gate::denies('access_expenses'), 403);

        return view('expense.expenses.index');

    }

    public function create()
    {
        abort_if(Gate::denies('create_expenses'), 403);

        return view('expense.expenses.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('create_expenses'), 403);

        $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'category_id' => 'required',
            'amount' => 'required|numeric|max:2147483647',
            'details' => 'nullable|string|max:1000',
        ]);

        $this->expenses->create([
            'date' => $request->date,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'details' => $request->details,
        ]);

        session()->flash('success', trans('expense.expense-created'));

        return redirect()->route('expenses.index');
    }

    public function edit(Expense $expense)
    {
        abort_if(Gate::denies('edit_expenses'), 403);

        return view('expense.expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        abort_if(Gate::denies('edit_expenses'), 403);

        $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'category_id' => 'required',
            'amount' => 'required|numeric|max:2147483647',
            'details' => 'nullable|string|max:1000',
        ]);

        $this->expenses->update($expense->id, [
            'date' => $request->date,
            'reference' => $request->reference,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'details' => $request->details,
        ]);

        session()->flash('info', trans('expense.expense-updated'));

        return redirect()->route('expenses.index');
    }

    public function destroy(Expense $expense)
    {
        abort_if(Gate::denies('delete_expenses'), 403);

        $this->expenses->delete($expense->id);

        session()->flash('warning', trans('expense.expense-deleted'));

        return redirect()->route('expenses.index');
    }
}
