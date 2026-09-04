<?php

namespace App\Livewire\Customers;

use App\Livewire\Imports\CsvImport;
use App\Models\Customer;
use Illuminate\Validation\Rule;

/**
 * Bulk-import customers (clients) from a CSV file.
 *
 * `client_type` accepts "physical_person" or "legal_entity" (defaulting to a
 * physical person); a tax identification number is required for legal entities.
 */
class CustomerImport extends CsvImport
{
    protected function gate(): string
    {
        return 'create_customers';
    }

    public function expectedColumns(): array
    {
        return [
            'customer_name',
            'client_type',
            'customer_phone',
            'customer_email',
            'whatsapp_number',
            'responsible_person',
            'tax_identification_number',
            'iban',
            'city',
            'country',
            'address',
            'note',
        ];
    }

    protected function requiredHeaders(): array
    {
        return ['customer_name', 'customer_email'];
    }

    protected function mapRow(array $raw, array &$errors): array
    {
        $clientType = strtolower((string) ($raw['client_type'] ?? ''));

        if ($clientType === '') {
            $clientType = Customer::TYPE_PHYSICAL_PERSON;
        }

        return [
            'customer_name' => $raw['customer_name'] ?? null,
            'client_type' => $clientType,
            'customer_phone' => $raw['customer_phone'] ?? null,
            'customer_email' => $raw['customer_email'] ?? null,
            'whatsapp_number' => $raw['whatsapp_number'] ?? null,
            'responsible_person' => $raw['responsible_person'] ?? null,
            'tax_identification_number' => $raw['tax_identification_number'] ?? null,
            'iban' => $raw['iban'] ?? null,
            'city' => $raw['city'] ?? null,
            'country' => $raw['country'] ?? null,
            'address' => $raw['address'] ?? null,
            'note' => $raw['note'] ?? null,
        ];
    }

    protected function rowRules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'client_type' => ['required', 'string', Rule::in(Customer::clientTypes())],
            'customer_phone' => ['required', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:255'],
            'responsible_person' => ['nullable', 'string', 'max:255'],
            'tax_identification_number' => [
                Rule::requiredIf(fn () => ($this->currentClientType ?? null) === Customer::TYPE_LEGAL_ENTITY),
                'nullable',
                'string',
                'max:255',
            ],
            'iban' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /** Client type of the row currently being validated (for conditional rules). */
    private ?string $currentClientType = null;

    protected function validateRow(array $attributes): array
    {
        $this->currentClientType = $attributes['client_type'] ?? null;

        return parent::validateRow($attributes);
    }

    protected function createRecord(array $attributes): void
    {
        Customer::create($attributes);
    }

    public function redirectRouteName(): string
    {
        return 'customers.index';
    }

    public function langPrefix(): string
    {
        return 'customer';
    }

    public function previewColumns(): array
    {
        return [
            'customer_name' => __('customer.customer_name'),
            'customer_email' => __('customer.customer_email'),
            'customer_phone' => __('customer.customer_phone'),
            'city' => __('customer.city'),
        ];
    }

    public function title(): string
    {
        return __('import.customers');
    }
}
