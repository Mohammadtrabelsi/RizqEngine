<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\SalePayment;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class CustomerShow extends Component
{
    public Customer $customer;

    public function mount(Customer $customer): void
    {
        abort_if(Gate::denies('show_customers'), 403);

        $this->customer = $customer;
    }

    public function render()
    {
        // Orders placed by this customer, most recent first, with their
        // recorded payments eager-loaded for the payments panel.
        $sales = $this->customer->sales()
            ->with('salePayments')
            ->latest()
            ->get();

        $payments = SalePayment::whereIn('sale_id', $sales->pluck('id'))
            ->latest()
            ->get();

        // Account totals across every order (accessors return real units).
        $totals = [
            'total' => $sales->sum(fn ($sale) => (float) $sale->total_amount),
            'paid' => $sales->sum(fn ($sale) => (float) $sale->paid_amount),
            'due' => $sales->sum(fn ($sale) => (float) $sale->due_amount),
        ];

        // Credit account (stored in cents => convert to the main unit).
        $credit = [
            'limit' => (int) $this->customer->credit_limit / 100,
            'balance' => (int) $this->customer->current_balance / 100,
            'available' => $this->customer->availableCredit() / 100,
            'allowed' => $this->customer->allowsCredit(),
            'over_limit' => $this->customer->isOverCreditLimit(),
        ];

        return view('livewire.customers.customer-show', compact('sales', 'payments', 'totals', 'credit'))
            ->layout('components.layouts.admin', ['title' => __('customer.customer_details')]);
    }
}
