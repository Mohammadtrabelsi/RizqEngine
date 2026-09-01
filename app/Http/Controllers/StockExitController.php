<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Models\StockExit;
use App\Services\StockExitService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class StockExitController extends Controller
{
    public function __construct(private StockExitService $stockExitService) {}

    public function index()
    {
        abort_if(Gate::denies('access_stock_exits'), 403);

        return view('stockexit.index');
    }

    public function create()
    {
        abort_if(Gate::denies('create_stock_exits'), 403);

        return view('stockexit.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('create_stock_exits'), 403);

        $validated = $request->validate([
            'date' => 'required|date',
            'reason' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'responsible' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|integer|exists:products,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|integer|min:1',
        ]);

        $lines = [];
        foreach ($validated['product_ids'] as $key => $id) {
            $lines[] = [
                'product_id' => (int) $id,
                'quantity' => (int) $validated['quantities'][$key],
            ];
        }

        try {
            $this->stockExitService->createExit($validated, $lines);
        } catch (InsufficientStockException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        session()->flash('success', trans('stockexit.exit-created'));

        return redirect()->route('stock-exits.index');
    }

    public function show(StockExit $stockExit)
    {
        abort_if(Gate::denies('show_stock_exits'), 403);

        $stockExit = $this->stockExitService->loadForShow($stockExit);

        return view('stockexit.show', compact('stockExit'));
    }

    public function destroy(StockExit $stockExit)
    {
        abort_if(Gate::denies('delete_stock_exits'), 403);

        $this->stockExitService->deleteModel($stockExit);

        session()->flash('warning', trans('stockexit.exit-deleted'));

        return redirect()->route('stock-exits.index');
    }
}
