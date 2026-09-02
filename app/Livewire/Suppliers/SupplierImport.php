<?php

namespace App\Livewire\Suppliers;

use App\Livewire\Imports\CsvImport;
use App\Models\Supplier;

/**
 * Bulk-import suppliers from a CSV file.
 */
class SupplierImport extends CsvImport
{
    protected function gate(): string
    {
        return 'create_suppliers';
    }

    public function expectedColumns(): array
    {
        return [
            'supplier_name',
            'supplier_phone',
            'supplier_email',
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
        return ['supplier_name', 'supplier_email'];
    }

    protected function mapRow(array $raw, array &$errors): array
    {
        return [
            'supplier_name' => $raw['supplier_name'] ?? null,
            'supplier_phone' => $raw['supplier_phone'] ?? null,
            'supplier_email' => $raw['supplier_email'] ?? null,
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
            'supplier_name' => ['required', 'string', 'max:255'],
            'supplier_phone' => ['required', 'max:255'],
            'supplier_email' => ['required', 'email', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:255'],
            'responsible_person' => ['nullable', 'string', 'max:255'],
            'tax_identification_number' => ['nullable', 'string', 'max:255'],
            'iban' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function createRecord(array $attributes): void
    {
        Supplier::create($attributes);
    }

    public function redirectRouteName(): string
    {
        return 'suppliers.index';
    }

    public function langPrefix(): string
    {
        return 'supplier';
    }

    public function previewColumns(): array
    {
        return [
            'supplier_name' => __('supplier.supplier_name'),
            'supplier_email' => __('supplier.supplier_email'),
            'supplier_phone' => __('supplier.supplier_phone'),
            'city' => __('supplier.city'),
        ];
    }

    public function title(): string
    {
        return __('import.suppliers');
    }
}
