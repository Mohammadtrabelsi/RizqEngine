<?php

namespace App\Services;

use App\Models\Tax;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Owns all persistence for taxes so Livewire components never touch the
 * Eloquent model directly.
 */
class TaxService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Tax::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data): Tax
    {
        return Tax::create($data);
    }

    public function update(int $id, array $data): Tax
    {
        $tax = Tax::findOrFail($id);
        $tax->update($data);

        return $tax;
    }

    public function delete(int $id): void
    {
        Tax::findOrFail($id)->delete();
    }
}
