<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ExpenseForm extends Component
{
    public ?int $expenseId = null;

    public string $reference = 'EXP';

    public string $date = '';

    public string $category_id = '';

    public string $amount = '';

    public string $details = '';

    public function mount(?Expense $expense = null): void
    {
        if ($expense && $expense->exists) {
            abort_if(Gate::denies('edit_expenses'), 403);

            $this->expenseId = $expense->id;
            $this->reference = (string) $expense->reference;
            $this->date = (string) $expense->getAttributes()['date'];
            $this->category_id = (string) $expense->category_id;
            $this->amount = (string) $expense->amount;
            $this->details = (string) $expense->details;
        } else {
            abort_if(Gate::denies('create_expenses'), 403);

            $this->date = now()->format('Y-m-d');
        }
    }

    protected function rules(): array
    {
        return [
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'category_id' => 'required',
            'amount' => 'required|numeric|min:0|max:2147483647',
            'details' => 'nullable|string|max:1000',
        ];
    }

    public function save(ExpenseService $expenses)
    {
        $this->validate();

        if ($this->expenseId) {
            abort_if(Gate::denies('edit_expenses'), 403);

            $expenses->update($this->expenseId, [
                'date' => $this->date,
                'reference' => $this->reference,
                'category_id' => $this->category_id,
                'amount' => $this->amount,
                'details' => $this->details,
            ]);

            session()->flash('info', trans('expense.expense-updated'));
        } else {
            abort_if(Gate::denies('create_expenses'), 403);

            $expenses->create([
                'date' => $this->date,
                'category_id' => $this->category_id,
                'amount' => $this->amount,
                'details' => $this->details,
            ]);

            session()->flash('success', trans('expense.expense-created'));
        }

        return redirect()->route('expenses.index');
    }

    public function render(ExpenseService $expenses)
    {
        return view('livewire.expenses.expense-form', [
            'categories' => $expenses->categories(),
        ]);
    }
}
