<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Owns all persistence for suppliers, including the optional profile image
 * kept in the "images" media collection.
 */
class SupplierService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Supplier::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('supplier_name', 'like', $term)
                    ->orWhere('supplier_email', 'like', $term)
                    ->orWhere('supplier_phone', 'like', $term);
            })
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(int $id): Supplier
    {
        return Supplier::findOrFail($id);
    }

    public function create(array $data, $image = null): Supplier
    {
        $supplier = Supplier::create($data);
        $this->syncImage($supplier, $image);

        return $supplier;
    }

    public function update(int $id, array $data, $image = null): Supplier
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update($data);
        $this->syncImage($supplier, $image);

        return $supplier;
    }

    public function delete(int $id): void
    {
        Supplier::findOrFail($id)->delete();
    }

    /**
     * Replace the supplier's image with the freshly uploaded file, if any.
     */
    protected function syncImage(Supplier $supplier, $image): void
    {
        if (! $image) {
            return;
        }

        if ($supplier->getFirstMedia('images')) {
            $supplier->getFirstMedia('images')->delete();
        }

        $supplier->addMedia($image->getRealPath())
            ->usingFileName($image->getClientOriginalName())
            ->toMediaCollection('images');
    }
}
