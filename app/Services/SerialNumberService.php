<?php

namespace App\Services;

use App\Enums\SerialStatus;
use App\Models\SerialNumber;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Owns all persistence for serialised units so Livewire components never touch
 * the Eloquent model directly.
 */
class SerialNumberService
{
    public function paginate(?string $search = null, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        return SerialNumber::query()
            ->with(['product', 'batch'])
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('serial', 'like', $term)
                    ->orWhereHas('product', fn ($q) => $q->where('product_code', 'like', $term));
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(array $data): SerialNumber
    {
        $data['status'] ??= SerialStatus::InStock->value;

        return SerialNumber::create($data);
    }

    public function update(int $id, array $data): SerialNumber
    {
        $serial = SerialNumber::findOrFail($id);
        $serial->update($data);

        return $serial;
    }

    /**
     * Transition a serialised unit to a new lifecycle status.
     */
    public function changeStatus(int $id, SerialStatus $status): SerialNumber
    {
        $serial = SerialNumber::findOrFail($id);
        $serial->update(['status' => $status]);

        return $serial;
    }

    public function delete(int $id): void
    {
        SerialNumber::findOrFail($id)->delete();
    }
}
