<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\StockInconsistencyException;
use App\Http\Controllers\Controller;
use App\Models\StockExit;
use App\Services\StockExitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API surface for {@see StockExitService} — the two-step "Sortie-Retour"
 * workflow (issue a Bon de Sortie, then close it with a Bon d'Entrée).
 */
class StockExitController extends Controller
{
    public function __construct(private readonly StockExitService $stockExits) {}

    /**
     * {@see StockExitService::createExit()}
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'date' => 'required|date',
            'reason' => 'nullable|string',
            'destination' => 'nullable|string',
            'responsible' => 'nullable|string',
            'note' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|integer|exists:products,id',
            'lines.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $stockExit = $this->stockExits->createExit(
                [
                    'date' => $data['date'],
                    'reason' => $data['reason'] ?? null,
                    'destination' => $data['destination'] ?? null,
                    'responsible' => $data['responsible'] ?? null,
                    'note' => $data['note'] ?? null,
                ],
                $data['lines'],
            );
        } catch (\App\Exceptions\InsufficientStockException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($stockExit->load('details'), 201);
    }

    /**
     * {@see StockExitService::createEntry()}
     */
    public function storeEntry(Request $request, StockExit $stockExit): JsonResponse
    {
        $data = $request->validate([
            'received' => 'required|array|min:1',
            'received.*.detail_id' => 'required|integer',
            'received.*.returned' => 'required|integer|min:0',
            'note' => 'nullable|string',
            'date' => 'nullable|date',
        ]);

        try {
            $entry = $this->stockExits->createEntry(
                $stockExit,
                $data['received'],
                $data['note'] ?? null,
                $data['date'] ?? null,
            );
        } catch (StockInconsistencyException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($entry->load('details'), 201);
    }
}
