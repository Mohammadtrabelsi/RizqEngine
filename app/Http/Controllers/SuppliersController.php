<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class SuppliersController extends Controller
{
    public function __construct(private readonly SupplierService $suppliers) {}

    public function index()
    {
        abort_if(Gate::denies('access_suppliers'), 403);

        return view('people.suppliers.index');
    }

    public function create()
    {
        abort_if(Gate::denies('create_suppliers'), 403);

        return view('people.suppliers.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('create_suppliers'), 403);

        $data = $this->validated($request);

        $this->suppliers->create($data, $request->file('image'));

        session()->flash('success', trans('people.supplier-created'));

        return redirect()->route('suppliers.index');
    }

    public function show(Supplier $supplier)
    {
        abort_if(Gate::denies('show_suppliers'), 403);

        return view('people.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        abort_if(Gate::denies('edit_suppliers'), 403);

        return view('people.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        abort_if(Gate::denies('edit_suppliers'), 403);

        $data = $this->validated($request);

        $this->suppliers->update($supplier->id, $data, $request->file('image'));

        session()->flash('info', trans('people.supplier-updated'));

        return redirect()->route('suppliers.index');
    }

    public function destroy(Supplier $supplier)
    {
        abort_if(Gate::denies('delete_suppliers'), 403);

        $this->suppliers->delete($supplier->id);

        session()->flash('warning', trans('people.supplier-deleted'));

        return redirect()->route('suppliers.index');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_phone' => 'required|max:255',
            'supplier_email' => 'required|email|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'address' => 'required|string|max:500',
        ]);
    }
}
