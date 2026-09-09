<?php

namespace App\Livewire\Vehicles;

use App\Livewire\Imports\CsvImport;
use App\Models\Vehicle;

/**
 * Bulk-import vehicles (véhicules) from a CSV file.
 */
class VehicleImport extends CsvImport
{
    protected function gate(): string
    {
        return 'create_vehicles';
    }

    public function expectedColumns(): array
    {
        return [
            'registration',
            'brand',
            'model',
            'note',
        ];
    }

    protected function requiredHeaders(): array
    {
        return ['registration'];
    }

    protected function mapRow(array $raw, array &$errors): array
    {
        return [
            'registration' => $raw['registration'] ?? null,
            'brand' => $raw['brand'] ?? null,
            'model' => $raw['model'] ?? null,
            'note' => $raw['note'] ?? null,
        ];
    }

    protected function rowRules(): array
    {
        return [
            'registration' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function createRecord(array $attributes): void
    {
        Vehicle::create($attributes);
    }

    public function redirectRouteName(): string
    {
        return 'vehicles.index';
    }

    public function langPrefix(): string
    {
        return 'vehicles';
    }

    public function previewColumns(): array
    {
        return [
            'registration' => __('vehicles.registration'),
            'brand' => __('vehicles.brand'),
            'model' => __('vehicles.model'),
        ];
    }

    public function title(): string
    {
        return __('import.vehicles');
    }

    public function exampleFile(): string
    {
        return 'vehicles.csv';
    }
}
