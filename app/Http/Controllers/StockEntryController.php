<?php

namespace App\Http\Controllers;

use App\Exceptions\StockInconsistencyException;
use App\Models\StockEntry;
use App\Models\StockExit;
use App\Services\StockExitService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class StockEntryController extends Controller
{
    public function __construct(private StockExitService $stockExitService) {}

    /**
     * Show the return-confirmation form for a specific Bon de Sortie.
     */
    public function create(StockExit $stockExit)
    {
        abort_if(Gate::denies('create_stock_entries'), 403);

        if ($stockExit->isClosed()) {
            return redirect()->route('stock-exits.show', $stockExit)
                ->with('error', trans('stockexit.exit-already-closed'));
        }

        $stockExit = $this->stockExitService->loadForEntry($stockExit);

        return view('stockentry.create', compact('stockExit'));
    }

    /**
     * Validate the physically confirmed return, restock what came back and
     * close the Bon de Sortie.
     */
    public function store(Request $request, StockExit $stockExit)
    {
        abort_if(Gate::denies('create_stock_entries'), 403);

        $validated = $request->validate([
            'date' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'detail_ids' => 'required|array|min:1',
            'detail_ids.*' => 'required|integer|exists:stock_exit_details,id',
            'returned' => 'required|array|min:1',
            'returned.*' => 'required|integer|min:0',
            'lost' => 'nullable|array',
            'lost.*' => 'nullable|integer|min:0',
        ]);

        $received = [];
        foreach ($validated['detail_ids'] as $key => $detailId) {
            $received[] = [
                'detail_id' => (int) $detailId,
                'returned' => (int) $validated['returned'][$key],
                'lost' => (int) ($validated['lost'][$key] ?? 0),
            ];
        }

        try {
            if ($stockExit->isConsignment()) {
                $this->stockExitService->createConsignmentReturn(
                    $stockExit,
                    $received,
                    $validated['note'] ?? null,
                    $validated['date'],
                );
            } else {
                $this->stockExitService->createEntry(
                    $stockExit,
                    $received,
                    $validated['note'] ?? null,
                    $validated['date'],
                );
            }
        } catch (StockInconsistencyException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        if ($stockExit->isConsignment()) {
            $message = trans('stockexit.consignment-regularised');
        } else {
            $message = $stockExit->fresh()->isClosed()
                ? trans('stockexit.entry-created-closed')
                : trans('stockexit.entry-created');
        }

        session()->flash('success', $message);

        return redirect()->route('stock-exits.show', $stockExit);
    }

    public function show(StockEntry $stockEntry)
    {
        abort_if(Gate::denies('show_stock_exits'), 403);

        $stockEntry = $this->stockExitService->loadEntryForShow($stockEntry);

        return view('stockentry.show', compact('stockEntry'));
    }
}
