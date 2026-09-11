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
    /**
     * @param  array{brand?: string}  $filters
     */
    public function paginate(?string $search = null, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return Vehicle::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('registration', 'like', $term)
                    ->orWhere('brand', 'like', $term)
                    ->orWhere('model', 'like', $term);
            })
            ->when($filters['brand'] ?? null, fn ($query, $brand) => $query->where('brand', $brand))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Distinct vehicle brands, for the index page's brand filter.
     *
     * @return array<int, string>
     */
    public function brands(): array
    {
        return Vehicle::query()
            ->whereNotNull('brand')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand')
            ->all();
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
