<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API surface for {@see CategoryService}. Every public service method is
 * exposed as a dedicated endpoint; this controller carries no business logic
 * of its own and only translates HTTP in and JSON out.
 */
class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categories) {}

    /**
     * {@see CategoryService::paginate()}
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->categories->paginate($request->query('search'), (int) $request->query('per_page', 12))
        );
    }

    /**
     * {@see CategoryService::nextCode()}
     */
    public function nextCode(): JsonResponse
    {
        return response()->json(['next_code' => $this->categories->nextCode()]);
    }

    /**
     * {@see CategoryService::findOrFail()}
     */
    public function show(int|string $id): JsonResponse
    {
        return response()->json($this->categories->findOrFail($id));
    }

    /**
     * {@see CategoryService::create()}
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_code' => 'required|unique:categories,category_code',
            'category_name' => 'required',
        ]);

        return response()->json($this->categories->create($data), 201);
    }

    /**
     * {@see CategoryService::update()}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'category_code' => 'required|unique:categories,category_code,'.$id,
            'category_name' => 'required',
        ]);

        return response()->json($this->categories->update($id, $data));
    }

    /**
     * {@see CategoryService::delete()}
     */
    public function destroy(int $id): JsonResponse
    {
        if (! $this->categories->delete($id)) {
            return response()->json(
                ['message' => 'Cannot delete a category that still has products associated with it.'],
                422
            );
        }

        return response()->json(['deleted' => true]);
    }
}
