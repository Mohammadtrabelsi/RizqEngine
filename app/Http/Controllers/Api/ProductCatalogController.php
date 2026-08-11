<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        return response()->json(
            $this->catalog->paginateByCategory(
                $request->query('category_id'),
                (int) $request->query('per_page', 12)
            )
        );
    }

    /**
     * {@see ProductCatalogService::search()}
     */
    public function search(Request $request): JsonResponse
    {
        $data = $request->validate([
            'term' => 'required|string',
            'limit' => 'nullable|integer|min:1',
        ]);

        return response()->json(
            $this->catalog->search($data['term'], (int) ($data['limit'] ?? 10))
        );
    }

    /**
     * {@see ProductCatalogService::findOrFail()}
     */
    public function show(int|string $id): JsonResponse
    {
        return response()->json($this->catalog->findOrFail($id));
    }
}
