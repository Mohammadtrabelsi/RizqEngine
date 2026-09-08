<?php

namespace App\Services;

use App\Models\WithholdingTax;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Owns all persistence for withholding taxes (retenues à la source) so
 * Livewire components never touch the Eloquent model directly, mirroring
 * {@see TaxService}.
 */
class WithholdingTaxService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return WithholdingTax::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('name', 'like', $term)
                    ->orWhere('code', 'like', $term);
            })
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Withholding taxes selectable on the given document side ("purchase" or
     * "sale"), active and effective on the given date.
     *
     * @return Collection<int, WithholdingTax>
     */
    public function selectableFor(string $side, ?Carbon $on = null): Collection
    {
        return WithholdingTax::query()
            ->effective($on)
            ->forSide($side)
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): WithholdingTax
    {
        return WithholdingTax::create($data);
    }

    public function update(int $id, array $data): WithholdingTax
    {
        $withholding = WithholdingTax::findOrFail($id);
        $withholding->update($data);

        return $withholding;
    }

    /**
     * Delete a withholding tax only when it has never been applied; otherwise
     * deactivate it so historical documents keep their snapshots.
     *
     * @return bool True when the record was deleted, false when it was
     *              deactivated because it is in use.
     */
    public function deleteOrDeactivate(int $id): bool
    {
        $withholding = WithholdingTax::findOrFail($id);

        if ($withholding->isUsed()) {
            $withholding->update(['active' => false]);

            return false;
        }

        $withholding->delete();

        return true;
    }
}
