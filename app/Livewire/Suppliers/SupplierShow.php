<?php

namespace App\Livewire\Suppliers;

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
        return view('livewire.suppliers.supplier-show')
            ->layout('components.layouts.admin', ['title' => __('supplier.supplier_details')]);
    }
}
