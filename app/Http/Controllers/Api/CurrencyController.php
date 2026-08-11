<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API surface for {@see CurrencyService}.
 */
class CurrencyController extends Controller
{
    public function __construct(private readonly CurrencyService $currencies) {}

    /**
     * {@see CurrencyService::paginate()}
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->currencies->paginate($request->query('search'), (int) $request->query('per_page', 12))
        );
    }

    /**
     * {@see CurrencyService::create()}
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json($this->currencies->create($this->validated($request)), 201);
    }

    /**
     * {@see CurrencyService::update()}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        return response()->json($this->currencies->update($id, $this->validated($request)));
    }

    /**
     * {@see CurrencyService::delete()}
     */
    public function destroy(int $id): JsonResponse
    {
        $this->currencies->delete($id);

        return response()->json(['deleted' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'currency_name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'symbol' => 'required|string|max:255',
            'thousand_separator' => 'required|string|max:255',
            'decimal_separator' => 'required|string|max:255',
        ]);
    }
}
