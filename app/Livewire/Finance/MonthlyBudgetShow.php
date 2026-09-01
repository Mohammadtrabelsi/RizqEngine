<?php

namespace App\Livewire\Finance;

use App\Models\FixedPayment;
use App\Models\MonthlyBudget;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * The month dashboard: shows the starting budget, the fixed payments and the
 * outings falling in the month, and the dynamically derived remaining balance.
 * Fixed payments are managed inline (add / delete) here.
 */
class MonthlyBudgetShow extends Component
{
    use WithFileUploads;

    public MonthlyBudget $budget;

    // Inline "add fixed payment" form.
    public string $label = '';

    public ?string $category = 'other';

    public string $amount = '0';

    public ?string $due_date = null;

    public $invoice; // uploaded file

    public ?string $note = null;

    public function mount(MonthlyBudget $monthlyBudget): void
    {
        $this->budget = $monthlyBudget;
    }

    protected function rules(): array
    {
        return [
            'label' => 'required|string|max:255',
            'category' => 'nullable|string|max:50',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'invoice' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
            'note' => 'nullable|string|max:2000',
        ];
    }

    public function addFixedPayment(): void
    {
        $data = $this->validate();

        $path = null;
        if ($this->invoice) {
            $path = $this->invoice->store('invoices', 'public');
        }

        FixedPayment::create([
            'monthly_budget_id' => $this->budget->id,
            'label' => $data['label'],
            'category' => $data['category'],
            'amount' => $data['amount'],
            'due_date' => $data['due_date'] ?: null,
            'invoice_path' => $path,
            'note' => $data['note'] ?? null,
        ]);

        $this->reset(['label', 'category', 'amount', 'due_date', 'invoice', 'note']);
        $this->category = 'other';
        $this->amount = '0';

        session()->flash('success', __('finance.payment_added'));
    }

    public function deleteFixedPayment(int $id): void
    {
        $payment = FixedPayment::where('monthly_budget_id', $this->budget->id)->findOrFail($id);

        if ($payment->invoice_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($payment->invoice_path);
        }

        $payment->delete();

        session()->flash('warning', __('finance.payment_deleted'));
    }

    public function render()
    {
        $this->budget->refresh();

        return view('livewire.finance.monthly-budget-show', [
            'fixedPayments' => $this->budget->fixedPayments()->orderBy('due_date')->get(),
            'outings' => $this->budget->outingsQuery()->orderBy('date')->get(),
            'totalFixed' => $this->budget->totalFixedPayments(),
            'totalOutings' => $this->budget->totalOutings(),
            'totalExpenses' => $this->budget->totalExpenses(),
            'remaining' => $this->budget->remainingBalance(),
        ])->layout('components.layouts.admin', ['title' => __('finance.monthly_budgets').' — '.$this->budget->label()]);
    }
}
