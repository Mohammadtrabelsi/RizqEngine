<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\PaymentsReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API surface for {@see PaymentsReportService}.
 */
class PaymentsReportController extends Controller
{
    public function __construct(private readonly PaymentsReportService $service) {}

    /**
     * {@see PaymentsReportService::paginate()}
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->service->paginate(
                $request->query('type'),
                $request->query('start_date'),
                $request->query('end_date'),
                $request->query('payment_method'),
                (int) $request->query('per_page', 12)
            )
        );
    }
}
