<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\StockMovementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API surface for {@see StockMovementService}.
 */
class StockMovementController extends Controller
{
    public function __construct(private readonly StockMovementService $service) {}

    /**
     * {@see StockMovementService::paginate()}
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->service->paginate(
                $request->query('start_date'),
                $request->query('end_date'),
                $request->query('product_id'),
                $request->query('type'),
                (int) $request->query('per_page', 12)
            )
        );
    }

    /**
     * {@see StockMovementService::products()}
     */
    public function products(): JsonResponse
    {
        return response()->json($this->service->products());
    }
}
