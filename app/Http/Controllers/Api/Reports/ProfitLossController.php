<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\ProfitLossService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API surface for {@see ProfitLossService}.
 */
class ProfitLossController extends Controller
{
    public function __construct(private readonly ProfitLossService $service) {}

    /**
     * {@see ProfitLossService::summary()}
     */
    public function summary(Request $request): JsonResponse
    {
        return response()->json(
            $this->service->summary($request->query('start_date'), $request->query('end_date'))
        );
    }
}
