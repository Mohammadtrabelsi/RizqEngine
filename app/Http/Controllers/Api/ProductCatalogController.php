<?php

namespace App\Http\Controllers\Api;

use App\Enums\StockStatus;
use App\Http\Controllers\Controller;
use App\Services\ProductCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

/**
 * API surface for {@see ProductCatalogService} (read access to the catalogue).
 */
class ProductCatalogController extends Controller
{
    public function __construct(private readonly ProductCatalogService $catalog) {}

    /**
     * {@see ProductCatalogService::paginateByCategory()}
     */
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id' => 'nullable|integer',
            'per_page' => 'nullable|integer|min:1',
            'stock_status' => ['nullable', new Enum(StockStatus::class)],
        ]);

        $products = $this->catalog->paginateByCategory(
            $data['category_id'] ?? null,
            (int) ($data['per_page'] ?? 12),
            isset($data['stock_status']) ? StockStatus::from($data['stock_status']) : null
        );

        $products->getCollection()->each->append('stock_status');

        return response()->json($products);
    }

    /**
     * {@see ProductCatalogService::search()}
     */
    public function search(Request $request): JsonResponse
    {
        $data = $request->validate([
            'term' => 'required|string',
            'limit' => 'nullable|integer|min:1',
            'category' => 'nullable|integer',
            'in_stock_only' => 'nullable|boolean',
        ]);

        return response()->json(
            $this->catalog->search($data['term'], (int) ($data['limit'] ?? 10), [
                'category' => $data['category'] ?? null,
                'in_stock_only' => (bool) ($data['in_stock_only'] ?? false),
            ])
        );
    }

    /**
     * {@see ProductCatalogService::findOrFail()}
     */
    public function show(int|string $id): JsonResponse
    {
        return response()->json(
            $this->catalog->findOrFail($id)->append('stock_status')
        );
    }
}
