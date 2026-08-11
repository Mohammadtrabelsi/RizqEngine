<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\ProductMovementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API surface for {@see ProductMovementService}.
 */
class ProductMovementController extends Controller
{
    public function __construct(private readonly ProductMovementService $service) {}

    /**
     * {@see ProductMovementService::ranked()}
     */
    public function ranked(Request $request): JsonResponse
    {
        $data = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'direction' => 'nullable|in:slow,fast',
            'limit' => 'nullable|integer|min:1',
        ]);

        return response()->json(
            $this->service->ranked(
                $data['start_date'],
                $data['end_date'],
                $data['direction'] ?? 'fast',
                (int) ($data['limit'] ?? 10)
            )
        );
    }
}
