<?php

namespace App\Services;

use App\Models\Driver;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Owns all persistence for drivers so Livewire components never touch the
 * Eloquent model directly.
 */
class DriverService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Driver::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('name', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('license_number', 'like', $term);
            })
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data): Driver
    {
        return Driver::create($data);
    }

    public function update(int $id, array $data): Driver
    {
        $driver = Driver::findOrFail($id);
        $driver->update($data);

        return $driver;
    }

    public function delete(int $id): void
    {
        Driver::findOrFail($id)->delete();
    }
}
