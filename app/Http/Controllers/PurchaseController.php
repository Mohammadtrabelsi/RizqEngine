<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
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

        $supplier = Supplier::findOrFail($purchase->supplier_id);

        return view('purchase.show', compact('purchase', 'supplier'));
    }

    public function edit(Purchase $purchase)
    {
        abort_if(Gate::denies('edit_purchases'), 403);

        $purchase_details = $purchase->purchaseDetails;

        Cart::instance('purchase')->destroy();

        $cart = Cart::instance('purchase');

        foreach ($purchase_details as $purchase_detail) {
            $cart->add([
                'id' => $purchase_detail->product_id,
                'name' => $purchase_detail->product_name,
                'qty' => $purchase_detail->quantity,
                'price' => $purchase_detail->price,
                'weight' => 1,
                'options' => [
                    'product_discount' => $purchase_detail->product_discount_amount,
                    'product_discount_type' => $purchase_detail->product_discount_type,
                    'sub_total' => $purchase_detail->sub_total,
                    'code' => $purchase_detail->product_code,
                    'stock' => Product::findOrFail($purchase_detail->product_id)->product_quantity,
                    'product_tax' => $purchase_detail->product_tax_amount,
                    'unit_price' => $purchase_detail->unit_price,
                ],
            ]);
        }

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

        $purchase->delete();

        session()->flash('warning', trans('purchase.purchase-deleted'));

        return redirect()->route('purchases.index');
    }
}
