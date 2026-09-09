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
    /**
     * @param  array{status?: string, city?: string}  $filters
     */
    public function paginate(?string $search = null, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return Warehouse::query()
            ->withCount('locations')
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('name', 'like', $term)
                    ->orWhere('code', 'like', $term)
                    ->orWhere('city', 'like', $term);
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('is_active', $status === 'active'))
            ->when($filters['city'] ?? null, fn ($query, $city) => $query->where('city', $city))
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Distinct, non-empty warehouse cities for the index filter dropdown.
     *
     * @return array<int, string>
     */
    public function cities(): array
    {
        return Warehouse::query()
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->all();
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
