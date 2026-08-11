<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UnitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API surface for {@see UnitService}.
 */
class UnitController extends Controller
{
    public function __construct(private readonly UnitService $units) {}

    /**
     * {@see UnitService::paginate()}
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->units->paginate($request->query('search'), (int) $request->query('per_page', 12))
        );
    }

    /**
     * {@see UnitService::create()}
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json($this->units->create($this->validated($request)), 201);
    }

    /**
     * {@see UnitService::update()}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        return response()->json($this->units->update($id, $this->validated($request)));
    }

    /**
     * {@see UnitService::delete()}
     */
    public function destroy(int $id): JsonResponse
    {
        $this->units->delete($id);

        return response()->json(['deleted' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'operator' => 'nullable|string|max:255',
            'operation_value' => 'nullable|string|max:255',
        ]);
    }
}
