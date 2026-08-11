<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\SalesReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API surface for {@see SalesReportService}.
 */
class SalesReportController extends Controller
{
    public function __construct(private readonly SalesReportService $service) {}

    /**
     * {@see SalesReportService::paginate()}
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->service->paginate(
                $request->query('start_date'),
                $request->query('end_date'),
                $request->query('customer_id'),
                $request->query('sale_status'),
                $request->query('payment_status'),
                (int) $request->query('per_page', 12)
            )
        );
    }
}
