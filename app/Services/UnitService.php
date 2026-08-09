<?php

namespace App\Services;

use App\Models\Unit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Owns all persistence for units so Livewire components (and any remaining
 * controllers) never touch the Eloquent model directly.
 */
class UnitService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Unit::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('name', 'like', $term)
                    ->orWhere('short_name', 'like', $term);
            })
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data): Unit
    {
        return Unit::create($data);
    }

    public function update(int $id, array $data): Unit
    {
        $unit = Unit::findOrFail($id);
        $unit->update($data);

        return $unit;
    }

    public function delete(int $id): void
    {
        Unit::findOrFail($id)->delete();
    }
}
