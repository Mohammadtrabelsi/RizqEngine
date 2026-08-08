<?php

namespace App\Http\Controllers;

use App\Models\AdjustedProduct;
use App\Models\Adjustment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AdjustmentController extends Controller
{
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

        $request->validate([
            'reference' => 'required|string|max:255',
            'date' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'product_ids' => 'required',
            'quantities' => 'required',
            'types' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $adjustment = Adjustment::create([
                'date' => $request->date,
                'note' => $request->note,
            ]);

            foreach ($request->product_ids as $key => $id) {
                AdjustedProduct::create([
                    'adjustment_id' => $adjustment->id,
                    'product_id' => $id,
                    'quantity' => $request->quantities[$key],
                    'type' => $request->types[$key],
                ]);

                $product = Product::findOrFail($id);

                if ($request->types[$key] == 'add') {
                    $product->update([
                        'product_quantity' => $product->product_quantity + $request->quantities[$key],
                    ]);
                } elseif ($request->types[$key] == 'sub') {
                    $product->update([
                        'product_quantity' => $product->product_quantity - $request->quantities[$key],
                    ]);
                }
            }
        });

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

        $request->validate([
            'reference' => 'required|string|max:255',
            'date' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'product_ids' => 'required',
            'quantities' => 'required',
            'types' => 'required',
        ]);

        DB::transaction(function () use ($request, $adjustment) {
            $adjustment->update([
                'reference' => $request->reference,
                'date' => $request->date,
                'note' => $request->note,
            ]);

            foreach ($adjustment->adjustedProducts as $adjustedProduct) {
                $product = Product::findOrFail($adjustedProduct->product->id);

                if ($adjustedProduct->type == 'add') {
                    $product->update([
                        'product_quantity' => $product->product_quantity - $adjustedProduct->quantity,
                    ]);
                } elseif ($adjustedProduct->type == 'sub') {
                    $product->update([
                        'product_quantity' => $product->product_quantity + $adjustedProduct->quantity,
                    ]);
                }

                $adjustedProduct->delete();
            }

            foreach ($request->product_ids as $key => $id) {
                AdjustedProduct::create([
                    'adjustment_id' => $adjustment->id,
                    'product_id' => $id,
                    'quantity' => $request->quantities[$key],
                    'type' => $request->types[$key],
                ]);

                $product = Product::findOrFail($id);

                if ($request->types[$key] == 'add') {
                    $product->update([
                        'product_quantity' => $product->product_quantity + $request->quantities[$key],
                    ]);
                } elseif ($request->types[$key] == 'sub') {
                    $product->update([
                        'product_quantity' => $product->product_quantity - $request->quantities[$key],
                    ]);
                }
            }
        });

        session()->flash('info', trans('adjustment.adjustment-updated'));

        return redirect()->route('adjustments.index');
    }

    public function destroy(Adjustment $adjustment)
    {
        abort_if(Gate::denies('delete_adjustments'), 403);

        $adjustment->delete();

        session()->flash('warning', trans('adjustment.adjustment-deleted'));

        return redirect()->route('adjustments.index');
    }
}
