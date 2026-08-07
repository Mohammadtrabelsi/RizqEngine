<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Unit;

class UnitsController extends Controller
{
    public function index()
    {
        $units = Unit::all();

        return view('setting.units.index', [
            'units' => $units,
        ]);
    }

    public function create()
    {
        return view('setting.units.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
        ]);

        Unit::create([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'operator' => $request->operator,
            'operation_value' => $request->operation_value,
        ]);

        session()->flash('success', trans('setting.unit-created'));

        return redirect()->route('units.index');
    }

    public function edit(Unit $unit)
    {
        return view('setting.units.edit', [
            'unit' => $unit,
        ]);
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
        ]);

        $unit->update([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'operator' => $request->operator,
            'operation_value' => $request->operation_value,
        ]);

        session()->flash('info', trans('setting.unit-updated'));

        return redirect()->route('units.index');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        session()->flash('warning', trans('setting.unit-deleted'));

        return redirect()->route('units.index');
    }
}
