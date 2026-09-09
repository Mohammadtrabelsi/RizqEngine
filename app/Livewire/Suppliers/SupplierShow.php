<?php

namespace App\Livewire\Suppliers;

use App\Models\PurchasePayment;
use App\Models\Supplier;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class SupplierShow extends Component
{
    public Supplier $supplier;

    public function mount(Supplier $supplier): void
    {
        abort_if(Gate::denies('show_suppliers'), 403);

        $this->supplier = $supplier;
    }

    public function render()
    {
        // Orders placed with this supplier, most recent first, with their
        // recorded payments eager-loaded for the payments panel.
        $purchases = $this->supplier->purchases()
            ->with('purchasePayments')
            ->latest()
            ->get();

        $payments = PurchasePayment::whereIn('purchase_id', $purchases->pluck('id'))
            ->latest()
            ->get();

        // Account totals across every order (accessors return real units).
        // The outstanding due is what the business still owes this supplier.
        $totals = [
            'total' => $purchases->sum(fn ($purchase) => (float) $purchase->total_amount),
            'paid' => $purchases->sum(fn ($purchase) => (float) $purchase->paid_amount),
            'due' => $purchases->sum(fn ($purchase) => (float) $purchase->due_amount),
        ];

        return view('livewire.suppliers.supplier-show', compact('purchases', 'payments', 'totals'))
            ->layout('components.layouts.admin', ['title' => __('supplier.supplier_details')]);
    }
}
