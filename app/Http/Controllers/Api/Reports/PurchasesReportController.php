<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\PurchasesReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API surface for {@see PurchasesReportService}.
 */
class PurchasesReportController extends Controller
{
    public function __construct(private readonly PurchasesReportService $service) {}

    /**
     * {@see PurchasesReportService::paginate()}
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->service->paginate(
                $request->query('start_date'),
                $request->query('end_date'),
                $request->query('supplier_id'),
                $request->query('purchase_status'),
                $request->query('payment_status'),
                (int) $request->query('per_page', 12)
            )
        );
    }
}
