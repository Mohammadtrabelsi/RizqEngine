<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExpenseCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * API surface for {@see ExpenseCategoryService}.
 */
class ExpenseCategoryController extends Controller
{
    public function __construct(private readonly ExpenseCategoryService $categories) {}

    /**
     * {@see ExpenseCategoryService::paginate()}
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->categories->paginate($request->query('search'), (int) $request->query('per_page', 12))
        );
    }

    /**
     * {@see ExpenseCategoryService::create()}
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json($this->categories->create($this->validated($request)), 201);
    }

    /**
     * {@see ExpenseCategoryService::update()}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        return response()->json($this->categories->update($id, $this->validated($request, $id)));
    }

    /**
     * {@see ExpenseCategoryService::delete()}
     */
    public function destroy(int $id): JsonResponse
    {
        if (! $this->categories->delete($id)) {
            return response()->json(
                ['message' => 'Cannot delete an expense category that still has expenses associated with it.'],
                422
            );
        }

        return response()->json(['deleted' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'category_name' => [
                'required', 'string', 'max:255',
                Rule::unique('expense_categories', 'category_name')->ignore($id),
            ],
            'category_description' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
