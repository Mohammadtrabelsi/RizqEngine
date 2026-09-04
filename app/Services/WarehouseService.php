<?php

namespace App\Services;

use App\Models\Warehouse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Owns all persistence for warehouses so Livewire components never touch the
 * Eloquent model directly.
 */
class WarehouseService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Warehouse::query()
            ->withCount('locations')
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('name', 'like', $term)
                    ->orWhere('code', 'like', $term)
                    ->orWhere('city', 'like', $term);
            })
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function create(array $data): Warehouse
    {
        return DB::transaction(function () use ($data) {
            $warehouse = Warehouse::create($data);
            $this->syncDefault($warehouse);

            return $warehouse;
        });
    }

    public function update(int $id, array $data): Warehouse
    {
        return DB::transaction(function () use ($id, $data) {
            $warehouse = Warehouse::findOrFail($id);
            $warehouse->update($data);
            $this->syncDefault($warehouse);

            return $warehouse;
        });
    }

    public function delete(int $id): void
    {
        Warehouse::findOrFail($id)->delete();
    }

    /**
     * Ensure at most one warehouse is flagged as the default.
     */
    protected function syncDefault(Warehouse $warehouse): void
    {
        if ($warehouse->is_default) {
            Warehouse::where('id', '!=', $warehouse->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
