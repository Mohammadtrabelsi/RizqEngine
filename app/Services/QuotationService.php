<?php

namespace App\Services;

use App\Models\Quotation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Read access to quotations for the Livewire layer.
 */
class QuotationService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Quotation::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('reference', 'like', $term)
                    ->orWhere('customer_name', 'like', $term);
            })
            ->latest()
            ->paginate($perPage);
    }
}
