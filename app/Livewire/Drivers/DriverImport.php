<?php

namespace App\Livewire\Drivers;

use App\Livewire\Imports\CsvImport;
use App\Models\Driver;

/**
 * Bulk-import drivers (chauffeurs) from a CSV file.
 */
class DriverImport extends CsvImport
{
    protected function gate(): string
    {
        return 'create_drivers';
    }

    public function expectedColumns(): array
    {
        return [
            'name',
            'phone',
            'license_number',
            'note',
        ];
    }

    protected function requiredHeaders(): array
    {
        return ['name'];
    }

    protected function mapRow(array $raw, array &$errors): array
    {
        return [
            'name' => $raw['name'] ?? null,
            'phone' => $raw['phone'] ?? null,
            'license_number' => $raw['license_number'] ?? null,
            'note' => $raw['note'] ?? null,
        ];
    }

    protected function rowRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function createRecord(array $attributes): void
    {
        Driver::create($attributes);
    }

    public function redirectRouteName(): string
    {
        return 'drivers.index';
    }

    public function langPrefix(): string
    {
        return 'drivers';
    }

    public function previewColumns(): array
    {
        return [
            'name' => __('drivers.name'),
            'phone' => __('drivers.phone'),
            'license_number' => __('drivers.license_number'),
        ];
    }

    public function title(): string
    {
        return __('import.drivers');
    }

    public function exampleFile(): string
    {
        return 'drivers.csv';
    }
}
