<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\InventoryValuationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API surface for {@see InventoryValuationService}.
 */
class InventoryValuationController extends Controller
{
    public function __construct(private readonly InventoryValuationService $service) {}

    /**
     * {@see InventoryValuationService::totals()}
     */
    public function totals(Request $request): JsonResponse
    {
        return response()->json($this->service->totals($request->query('category_id')));
    }

    /**
     * {@see InventoryValuationService::paginateProducts()}
     */
    public function products(Request $request): JsonResponse
    {
        return response()->json(
            $this->service->paginateProducts(
                $request->query('category_id'),
                $request->query('search'),
                (int) $request->query('per_page', 12)
            )
        );
    }

    /**
     * {@see InventoryValuationService::categories()}
     */
    public function categories(): JsonResponse
    {
        return response()->json($this->service->categories());
    }
}
