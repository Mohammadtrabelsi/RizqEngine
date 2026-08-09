<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

/**
 * Owns all persistence for currencies. Normalises the ISO code to upper case
 * so callers do not have to remember to.
 */
class CurrencyService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Currency::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('currency_name', 'like', $term)
                    ->orWhere('code', 'like', $term);
            })
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Currency
    {
        return Currency::create($this->normalize($data));
    }

    public function update(int $id, array $data): Currency
    {
        $currency = Currency::findOrFail($id);
        $currency->update($this->normalize($data));

        return $currency;
    }

    public function delete(int $id): void
    {
        Currency::findOrFail($id)->delete();
    }

    protected function normalize(array $data): array
    {
        if (isset($data['code'])) {
            $data['code'] = Str::upper($data['code']);
        }

        return $data;
    }
}
