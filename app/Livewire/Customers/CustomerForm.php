<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class CustomerForm extends Component
{
    public ?int $customerId = null;

    public string $customer_name = '';

    public string $customer_email = '';

    public string $customer_phone = '';

    public string $city = '';

    public string $country = '';

    public string $address = '';

    public function mount(?Customer $customer = null): void
    {
        if ($customer && $customer->exists) {
            abort_if(Gate::denies('update_customers'), 403);

            $this->customerId = $customer->id;
            $this->customer_name = (string) $customer->customer_name;
            $this->customer_email = (string) $customer->customer_email;
            $this->customer_phone = (string) $customer->customer_phone;
            $this->city = (string) $customer->city;
            $this->country = (string) $customer->country;
            $this->address = (string) $customer->address;
        } else {
            abort_if(Gate::denies('create_customers'), 403);
        }
    }

    protected function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|max:255',
            'customer_email' => 'required|email|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'address' => 'required|string|max:500',
        ];
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->customerId) {
            abort_if(Gate::denies('update_customers'), 403);
            Customer::findOrFail($this->customerId)->update($data);
            session()->flash('info', trans('people.customer-updated'));
        } else {
            abort_if(Gate::denies('create_customers'), 403);
            Customer::create($data);
            session()->flash('success', trans('people.customer-created'));
        }

        return redirect()->route('customers.index');
    }

    public function render()
    {
        return view('livewire.customers.customer-form');
    }
}
