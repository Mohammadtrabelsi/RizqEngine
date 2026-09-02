<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Owns all persistence for vehicles so Livewire components never touch the
 * Eloquent model directly.
 */
class VehicleService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Vehicle::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('registration', 'like', $term)
                    ->orWhere('brand', 'like', $term)
                    ->orWhere('model', 'like', $term);
            })
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data): Vehicle
    {
        return Vehicle::create($data);
    }

    public function update(int $id, array $data): Vehicle
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->update($data);

        return $vehicle;
    }

    public function delete(int $id): void
    {
        Vehicle::findOrFail($id)->delete();
    }
}
