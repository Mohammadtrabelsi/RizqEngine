<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Owns all persistence for suppliers, including the optional profile image
 * kept in the "images" media collection.
 */
class SupplierService
{
    /**
     * All suppliers ordered by name, for populating filter/select controls.
     *
     * @return Collection<int, Supplier>
     */
    public function ordered(): Collection
    {
        return Supplier::orderBy('supplier_name')->get();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(?string $search = null, int $perPage = 12, array $filters = []): LengthAwarePaginator
    {
        $city = $filters['city'] ?? '';
        $country = $filters['country'] ?? '';
        $hasTaxId = $filters['hasTaxId'] ?? '';

        return Supplier::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('supplier_name', 'like', $term)
                        ->orWhere('supplier_email', 'like', $term)
                        ->orWhere('supplier_phone', 'like', $term)
                        ->orWhere('tax_identification_number', 'like', $term);
                });
            })
            ->when($city !== '', function ($query) use ($city) {
                $query->where('city', $city);
            })
            ->when($country !== '', function ($query) use ($country) {
                $query->where('country', $country);
            })
            ->when($hasTaxId === 'yes', function ($query) {
                $query->whereNotNull('tax_identification_number')
                    ->where('tax_identification_number', '!=', '');
            })
            ->when($hasTaxId === 'no', function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('tax_identification_number')
                        ->orWhere('tax_identification_number', '=', '');
                });
            })
            ->latest()
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Distinct non-empty cities across all suppliers.
     *
     * @return Collection<int, string>
     */
    public function cities()
    {
        return Supplier::query()
            ->whereNotNull('city')->where('city', '!=', '')
            ->distinct()->orderBy('city')->pluck('city');
    }

    /**
     * Distinct non-empty countries across all suppliers.
     *
     * @return Collection<int, string>
     */
    public function countries()
    {
        return Supplier::query()
            ->whereNotNull('country')->where('country', '!=', '')
            ->distinct()->orderBy('country')->pluck('country');
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
