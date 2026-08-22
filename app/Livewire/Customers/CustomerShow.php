<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
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
        return view('livewire.customers.customer-show');
    }
}
