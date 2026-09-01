<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturnPayment;
use App\Services\PurchaseReturnService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class PurchaseReturnPaymentsController extends Controller
{
    public function __construct(private readonly PurchaseReturnService $purchaseReturns) {}

    public function index($purchase_return_id)
    {
        abort_if(Gate::denies('access_purchase_return_payments'), 403);

        $purchase_return = $this->purchaseReturns->findOrFail($purchase_return_id);

        return view('purchasesreturn.payments.index', compact('purchase_return'));

    }

    public function create($purchase_return_id)
    {
        abort_if(Gate::denies('access_purchase_return_payments'), 403);

        $purchase_return = $this->purchaseReturns->findOrFail($purchase_return_id);

        return view('purchasesreturn.payments.create', compact('purchase_return'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('access_purchase_return_payments'), 403);

        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'note' => 'nullable|string|max:1000',
            'purchase_return_id' => 'required',
            'payment_method' => 'required|string|max:255',
        ]);

        $this->purchaseReturns->addPayment($data);

        session()->flash('success', trans('purchasesreturn.purchase-return-payment-created'));

        return redirect()->route('purchase-returns.index');
    }

    public function edit($purchase_return_id, PurchaseReturnPayment $purchaseReturnPayment)
    {
        abort_if(Gate::denies('access_purchase_return_payments'), 403);

        $purchase_return = $this->purchaseReturns->findOrFail($purchase_return_id);

        return view('purchasesreturn.payments.edit', compact('purchaseReturnPayment', 'purchase_return'));
    }

    public function update(Request $request, PurchaseReturnPayment $purchaseReturnPayment)
    {
        abort_if(Gate::denies('access_purchase_return_payments'), 403);

        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'note' => 'nullable|string|max:1000',
            'purchase_return_id' => 'required',
            'payment_method' => 'required|string|max:255',
        ]);

        $this->purchaseReturns->updatePayment($purchaseReturnPayment, $data);

        session()->flash('info', trans('purchasesreturn.purchase-return-payment-updated'));

        return redirect()->route('purchase-returns.index');
    }

    public function destroy(PurchaseReturnPayment $purchaseReturnPayment)
    {
        abort_if(Gate::denies('access_purchase_return_payments'), 403);

        $this->purchaseReturns->deletePayment($purchaseReturnPayment->id);

        session()->flash('warning', trans('purchasesreturn.purchase-return-payment-deleted'));

        return redirect()->route('purchase-returns.index');
    }
}
