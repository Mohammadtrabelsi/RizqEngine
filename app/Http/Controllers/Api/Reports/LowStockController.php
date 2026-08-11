<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\LowStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API surface for {@see LowStockService}.
 */
class LowStockController extends Controller
{
    public function __construct(private readonly LowStockService $service) {}

    /**
     * {@see LowStockService::paginate()}
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->service->paginate(
                $request->boolean('only_out_of_stock'),
                (int) $request->query('per_page', 12)
            )
        );
    }

    /**
     * {@see LowStockService::outOfStockCount()}
     */
    public function outOfStockCount(): JsonResponse
    {
        return response()->json(['out_of_stock_count' => $this->service->outOfStockCount()]);
    }

    /**
     * {@see LowStockService::lowStockCount()}
     */
    public function lowStockCount(): JsonResponse
    {
        return response()->json(['low_stock_count' => $this->service->lowStockCount()]);
    }
}
