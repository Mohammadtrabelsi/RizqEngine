<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\Purchase;
use App\Services\PurchaseService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class PurchaseController extends Controller
{
    public function __construct(private readonly PurchaseService $purchases) {}

    public function index()
    {
        abort_if(Gate::denies('access_purchases'), 403);

        return view('purchase.index');

    }

    public function create()
    {
        abort_if(Gate::denies('create_purchases'), 403);

        Cart::instance('purchase')->destroy();

        return view('purchase.create');
    }

    public function store(StorePurchaseRequest $request)
    {
        abort_if(Gate::denies('create_purchases'), 403);

        try {
            $this->purchases->createPurchase($request->all());
        } catch (InsufficientStockException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        session()->flash('success', trans('purchase.purchase-created'));

        return redirect()->route('purchases.index');
    }

    public function show(Purchase $purchase)
    {
        abort_if(Gate::denies('show_purchases'), 403);

        $supplier = $this->purchases->supplierFor($purchase);

        return view('purchase.show', compact('purchase', 'supplier'));
    }

    public function edit(Purchase $purchase)
    {
        abort_if(Gate::denies('edit_purchases'), 403);

        $this->purchases->loadCart($purchase);

        return view('purchase.edit', compact('purchase'));
    }

    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        abort_if(Gate::denies('edit_purchases'), 403);

        try {
            $this->purchases->updatePurchase($purchase, $request->all());
        } catch (InsufficientStockException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        session()->flash('info', trans('purchase.purchase-updated'));

        return redirect()->route('purchases.index');
    }

    public function destroy(Purchase $purchase)
    {
        abort_if(Gate::denies('delete_purchases'), 403);

        $this->purchases->delete($purchase);

        session()->flash('warning', trans('purchase.purchase-deleted'));

        return redirect()->route('purchases.index');
    }
}
