<?php

namespace App\Services\Reports;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\StockMovement;

/**
 * Data access for the stock movement report.
 */
class StockMovementService
{
    /**
     * Paginate stock movements within a date range, optionally filtered by
     * product and movement type.
     */
    public function paginate(
        ?string $startDate,
        ?string $endDate,
        int|string|null $productId,
        ?string $type,
        int $perPage
    ): LengthAwarePaginator {
        return StockMovement::with(['product', 'user'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->when($productId, fn ($q) => $q->where('product_id', $productId))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Products available as report filters.
     */
    public function products(): Collection
    {
        return Product::orderBy('product_name')->get(['id', 'product_name']);
    }
}
