<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class CustomerForm extends Component
{
    use WithFileUploads;

    public ?int $customerId = null;

    public ?Customer $customer = null;

    public string $customer_name = '';

    public string $client_type = Customer::TYPE_PHYSICAL_PERSON;

    public string $customer_email = '';

    public string $customer_phone = '';

    public string $whatsapp_number = '';

    public string $responsible_person = '';

    public string $tax_identification_number = '';

    public string $iban = '';

    public string $city = '';

    public string $country = '';

    public string $address = '';

    public string $note = '';

    public $image;

    public $document;

    public function mount(?Customer $customer = null): void
    {
        if ($customer && $customer->exists) {
            abort_if(Gate::denies('update_customers'), 403);

            $this->customer = $customer;
            $this->customerId = $customer->id;
            $this->customer_name = (string) $customer->customer_name;
            $this->client_type = (string) ($customer->client_type ?: Customer::TYPE_PHYSICAL_PERSON);
            $this->customer_email = (string) $customer->customer_email;
            $this->customer_phone = (string) $customer->customer_phone;
            $this->whatsapp_number = (string) $customer->whatsapp_number;
            $this->responsible_person = (string) $customer->responsible_person;
            $this->tax_identification_number = (string) $customer->tax_identification_number;
            $this->iban = (string) $customer->iban;
            $this->city = (string) $customer->city;
            $this->country = (string) $customer->country;
            $this->address = (string) $customer->address;
            $this->note = (string) $customer->note;
        } else {
            abort_if(Gate::denies('create_customers'), 403);
        }
    }

    protected function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'client_type' => ['required', 'string', Rule::in(Customer::clientTypes())],
            'customer_phone' => 'required|max:255',
            'customer_email' => 'required|email|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'responsible_person' => 'nullable|string|max:255',
            // The tax identification number (matricule fiscal) is mandatory
            // for legal entities and optional for physical persons.
            'tax_identification_number' => [
                Rule::requiredIf($this->client_type === Customer::TYPE_LEGAL_ENTITY),
                'nullable',
                'string',
                'max:255',
            ],
            'iban' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'note' => 'nullable|string|max:2000',
            'image' => 'nullable|image|max:2048',
            'document' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt,jpg,jpeg,png|max:10240',
        ];
    }

    protected function messages(): array
    {
        return [
            'tax_identification_number.required' => trans('customer.legal_entity_tax_required'),
        ];
    }

    public function save(CustomerService $customers)
    {
        $data = $this->validate();

        $image = $data['image'] ?? null;
        unset($data['image']);

        $document = $data['document'] ?? null;
        unset($data['document']);

        if ($this->customerId) {
            abort_if(Gate::denies('update_customers'), 403);
            $customers->update($this->customerId, $data, $image, $document);

            session()->flash('info', trans('people.customer-updated'));
        } else {
            abort_if(Gate::denies('create_customers'), 403);
            $customers->create($data, $image, $document);

            session()->flash('success', trans('people.customer-created'));
        }

        return redirect()->route('customers.index');
    }

    public function render()
    {
        return view('livewire.customers.customer-form')
            ->layout('components.layouts.admin', ['title' => $this->customerId ? __('customer.edit') : __('customer.create')]);
    }
}
