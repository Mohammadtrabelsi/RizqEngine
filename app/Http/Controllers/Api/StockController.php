<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API surface for {@see StockService} — programmatic stock movements against a
 * product, each recorded in the stock ledger.
 */
class StockController extends Controller
{
    public function __construct(private readonly StockService $stock) {}

    /**
     * {@see StockService::stockIn()}
     */
    public function stockIn(Request $request, Product $product): JsonResponse
    {
        $data = $this->validateMovement($request);

        $result = $this->stock->stockIn(
            $product,
            (int) $data['quantity'],
            $data['note'] ?? null,
            $data['reference_type'] ?? 'Manual',
            isset($data['reference_id']) ? (int) $data['reference_id'] : null,
        );

        return response()->json($result);
    }

    /**
     * {@see StockService::stockOut()}
     */
    public function stockOut(Request $request, Product $product): JsonResponse
    {
        $data = $this->validateMovement($request);

        try {
            $result = $this->stock->stockOut(
                $product,
                (int) $data['quantity'],
                $data['note'] ?? null,
                $data['reference_type'] ?? 'Manual',
                isset($data['reference_id']) ? (int) $data['reference_id'] : null,
            );
        } catch (InsufficientStockException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($result);
    }

    /**
     * {@see StockService::setQuantity()}
     */
    public function setQuantity(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:0',
            'note' => 'nullable|string',
        ]);

        try {
            $result = $this->stock->setQuantity($product, (int) $data['quantity'], $data['note'] ?? null);
        } catch (InsufficientStockException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($result);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateMovement(Request $request): array
    {
        return $request->validate([
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
        ]);
    }
}
