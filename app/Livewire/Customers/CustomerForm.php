<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;

class CustomerForm extends Component
{
    use WithFileUploads;

    public ?int $customerId = null;

    public ?Customer $customer = null;

    public string $customer_name = '';

    public string $customer_email = '';

    public string $customer_phone = '';

    public string $city = '';

    public string $country = '';

    public string $address = '';

    public $image;

    public function mount(?Customer $customer = null): void
    {
        if ($customer && $customer->exists) {
            abort_if(Gate::denies('update_customers'), 403);

            $this->customer = $customer;
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
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function save()
    {
        $data = $this->validate();

        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($this->customerId) {
            abort_if(Gate::denies('update_customers'), 403);
            $customer = Customer::findOrFail($this->customerId);
            $customer->update($data);

            if ($image) {
                if ($customer->getFirstMedia('images')) {
                    $customer->getFirstMedia('images')->delete();
                }

                $customer->addMedia($image->getRealPath())
                    ->usingFileName($image->getClientOriginalName())
                    ->toMediaCollection('images');
            }

            session()->flash('info', trans('people.customer-updated'));
        } else {
            abort_if(Gate::denies('create_customers'), 403);
            $customer = Customer::create($data);

            if ($image) {
                $customer->addMedia($image->getRealPath())
                    ->usingFileName($image->getClientOriginalName())
                    ->toMediaCollection('images');
            }

            session()->flash('success', trans('people.customer-created'));
        }

        return redirect()->route('customers.index');
    }

    public function render()
    {
        return view('livewire.customers.customer-form');
    }
}
