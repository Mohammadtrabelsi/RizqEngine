<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\PurchasesReturnReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API surface for {@see PurchasesReturnReportService}.
 */
class PurchasesReturnReportController extends Controller
{
    public function __construct(private readonly PurchasesReturnReportService $service) {}

    /**
     * {@see PurchasesReturnReportService::paginate()}
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->service->paginate(
                $request->query('start_date'),
                $request->query('end_date'),
                $request->query('supplier_id'),
                $request->query('purchase_return_status'),
                $request->query('payment_status'),
                (int) $request->query('per_page', 12)
            )
        );
    }
}
