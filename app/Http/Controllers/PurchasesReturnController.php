<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\StorePurchaseReturnRequest;
use App\Http\Requests\UpdatePurchaseReturnRequest;
use App\Models\PurchaseReturn;
use App\Services\PurchaseReturnService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class PurchasesReturnController extends Controller
{
    public function __construct(private readonly PurchaseReturnService $purchaseReturns) {}

    public function index()
    {
        abort_if(Gate::denies('access_purchase_returns'), 403);

        return view('purchasesreturn.index');

    }

    public function create()
    {
        abort_if(Gate::denies('create_purchase_returns'), 403);

        Cart::instance('purchase_return')->destroy();

        return view('purchasesreturn.create');
    }

    public function store(StorePurchaseReturnRequest $request)
    {
        abort_if(Gate::denies('create_purchase_returns'), 403);

        try {
            $this->purchaseReturns->createPurchaseReturn($request->all());
        } catch (InsufficientStockException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        session()->flash('success', trans('purchasesreturn.purchase-return-created'));

        return redirect()->route('purchase-returns.index');
    }

    public function show(PurchaseReturn $purchase_return)
    {
        abort_if(Gate::denies('show_purchase_returns'), 403);

        $supplier = $this->purchaseReturns->supplierFor($purchase_return);

        return view('purchasesreturn.show', compact('purchase_return', 'supplier'));
    }

    public function edit(PurchaseReturn $purchase_return)
    {
        abort_if(Gate::denies('edit_purchase_returns'), 403);

        $this->purchaseReturns->loadCart($purchase_return);

        return view('purchasesreturn.edit', compact('purchase_return'));
    }

    public function update(UpdatePurchaseReturnRequest $request, PurchaseReturn $purchase_return)
    {
        abort_if(Gate::denies('edit_purchase_returns'), 403);

        try {
            $this->purchaseReturns->updatePurchaseReturn($purchase_return, $request->all());
        } catch (InsufficientStockException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        session()->flash('info', trans('purchasesreturn.purchase-return-updated'));

        return redirect()->route('purchase-returns.index');
    }

    public function destroy(PurchaseReturn $purchase_return)
    {
        abort_if(Gate::denies('delete_purchase_returns'), 403);

        $this->purchaseReturns->delete($purchase_return);

        session()->flash('warning', trans('purchasesreturn.purchase-return-deleted'));

        return redirect()->route('purchase-returns.index');
    }
}
