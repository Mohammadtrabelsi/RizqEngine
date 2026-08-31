<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Models\Adjustment;
use App\Services\AdjustmentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class AdjustmentController extends Controller
{
    public function __construct(private readonly AdjustmentService $adjustments) {}

    public function index()
    {
        abort_if(Gate::denies('access_adjustments'), 403);

        return view('adjustment.index');

    }

    public function create()
    {
        abort_if(Gate::denies('create_adjustments'), 403);

        return view('adjustment.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('create_adjustments'), 403);

        $data = $request->validate([
            'reference' => 'required|string|max:255',
            'date' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'product_ids' => 'required',
            'quantities' => 'required',
            'types' => 'required',
        ]);

        try {
            $this->adjustments->createAdjustment($data);
        } catch (InsufficientStockException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        session()->flash('success', trans('adjustment.adjustment-created'));

        return redirect()->route('adjustments.index');
    }

    public function show(Adjustment $adjustment)
    {
        abort_if(Gate::denies('show_adjustments'), 403);

        return view('adjustment.show', compact('adjustment'));
    }

    public function edit(Adjustment $adjustment)
    {
        abort_if(Gate::denies('edit_adjustments'), 403);

        return view('adjustment.edit', compact('adjustment'));
    }

    public function update(Request $request, Adjustment $adjustment)
    {
        abort_if(Gate::denies('edit_adjustments'), 403);

        $data = $request->validate([
            'reference' => 'required|string|max:255',
            'date' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'product_ids' => 'required',
            'quantities' => 'required',
            'types' => 'required',
        ]);

        try {
            $this->adjustments->updateAdjustment($adjustment, $data);
        } catch (InsufficientStockException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        session()->flash('info', trans('adjustment.adjustment-updated'));

        return redirect()->route('adjustments.index');
    }

    public function destroy(Adjustment $adjustment)
    {
        abort_if(Gate::denies('delete_adjustments'), 403);

        $this->adjustments->deleteModel($adjustment);

        session()->flash('warning', trans('adjustment.adjustment-deleted'));

        return redirect()->route('adjustments.index');
    }
}
