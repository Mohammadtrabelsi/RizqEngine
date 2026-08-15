<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class PurchasePaymentsController extends Controller
{
    public function __construct(private readonly PurchaseService $purchases) {}

    public function index($purchase_id)
    {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $purchase = Purchase::findOrFail($purchase_id);

        return view('purchase.payments.index', compact('purchase'));

    }

    public function create($purchase_id)
    {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $purchase = Purchase::findOrFail($purchase_id);

        return view('purchase.payments.create', compact('purchase'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'note' => 'nullable|string|max:1000',
            'purchase_id' => 'required',
            'payment_method' => 'required|string|max:255',
        ]);

        $this->purchases->addPayment($data);

        session()->flash('success', trans('purchase.purchase-payment-created'));

        return redirect()->route('purchases.index');
    }

    public function edit($purchase_id, PurchasePayment $purchasePayment)
    {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $purchase = Purchase::findOrFail($purchase_id);

        return view('purchase.payments.edit', compact('purchasePayment', 'purchase'));
    }

    public function update(Request $request, PurchasePayment $purchasePayment)
    {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'note' => 'nullable|string|max:1000',
            'purchase_id' => 'required',
            'payment_method' => 'required|string|max:255',
        ]);

        $this->purchases->updatePayment($purchasePayment, $data);

        session()->flash('info', trans('purchase.purchase-payment-updated'));

        return redirect()->route('purchases.index');
    }

    public function destroy(PurchasePayment $purchasePayment)
    {
        abort_if(Gate::denies('access_purchase_payments'), 403);

        $purchasePayment->delete();

        session()->flash('warning', trans('purchase.purchase-payment-deleted'));

        return redirect()->route('purchases.index');
    }
}
