<?php

namespace App\Services;

use App\Models\Batch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Owns all persistence for product batches (lots) so Livewire components never
 * touch the Eloquent model directly.
 */
class BatchService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Batch::query()
            ->with(['product', 'warehouse'])
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('batch_number', 'like', $term)
                    ->orWhereHas('product', fn ($q) => $q->where('product_code', 'like', $term));
            })
            ->orderBy('expiry_date')
            ->paginate($perPage);
    }

    public function create(array $data): Batch
    {
        return Batch::create($data);
    }

    public function update(int $id, array $data): Batch
    {
        $batch = Batch::findOrFail($id);
        $batch->update($data);

        return $batch;
    }

    public function delete(int $id): void
    {
        Batch::findOrFail($id)->delete();
    }

    /**
     * Batches expiring within the given horizon, soonest first.
     */
    public function expiringWithin(int $days = 30): LengthAwarePaginator
    {
        return Batch::query()
            ->with('product')
            ->expiringWithin($days)
            ->orderBy('expiry_date')
            ->paginate(12);
    }
}
