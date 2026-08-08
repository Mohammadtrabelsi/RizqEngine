<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnPayment;

class PurchaseReturnPaymentsController extends Controller
{
    public function index($purchase_return_id)
    {
        abort_if(Gate::denies('access_purchase_return_payments'), 403);

        $purchase_return = PurchaseReturn::findOrFail($purchase_return_id);

        $payments = PurchaseReturnPayment::where('purchase_return_id', $purchase_return_id)->latest()->paginate(12);

        return view('purchasesreturn.payments.index', compact('purchase_return', 'payments'));
    }

    public function create($purchase_return_id)
    {
        abort_if(Gate::denies('access_purchase_return_payments'), 403);

        $purchase_return = PurchaseReturn::findOrFail($purchase_return_id);

        return view('purchasesreturn.payments.create', compact('purchase_return'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('access_purchase_return_payments'), 403);

        $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'note' => 'nullable|string|max:1000',
            'purchase_return_id' => 'required',
            'payment_method' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            PurchaseReturnPayment::create([
                'date' => $request->date,
                'reference' => $request->reference,
                'amount' => $request->amount,
                'note' => $request->note,
                'purchase_return_id' => $request->purchase_return_id,
                'payment_method' => $request->payment_method,
            ]);

            $purchase_return = PurchaseReturn::findOrFail($request->purchase_return_id);

            $due_amount = $purchase_return->due_amount - $request->amount;

            if ($due_amount == $purchase_return->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $purchase_return->update([
                'paid_amount' => ($purchase_return->paid_amount + $request->amount) * 100,
                'due_amount' => $due_amount * 100,
                'payment_status' => $payment_status,
            ]);
        });

        session()->flash('success', trans('purchasesreturn.purchase-return-payment-created'));

        return redirect()->route('purchase-returns.index');
    }

    public function edit($purchase_return_id, PurchaseReturnPayment $purchaseReturnPayment)
    {
        abort_if(Gate::denies('access_purchase_return_payments'), 403);

        $purchase_return = PurchaseReturn::findOrFail($purchase_return_id);

        return view('purchasesreturn.payments.edit', compact('purchaseReturnPayment', 'purchase_return'));
    }

    public function update(Request $request, PurchaseReturnPayment $purchaseReturnPayment)
    {
        abort_if(Gate::denies('access_purchase_return_payments'), 403);

        $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'note' => 'nullable|string|max:1000',
            'purchase_return_id' => 'required',
            'payment_method' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $purchaseReturnPayment) {
            $purchase_return = $purchaseReturnPayment->purchaseReturn;

            $due_amount = ($purchase_return->due_amount + $purchaseReturnPayment->amount) - $request->amount;

            if ($due_amount == $purchase_return->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $purchase_return->update([
                'paid_amount' => (($purchase_return->paid_amount - $purchaseReturnPayment->amount) + $request->amount) * 100,
                'due_amount' => $due_amount * 100,
                'payment_status' => $payment_status,
            ]);

            $purchaseReturnPayment->update([
                'date' => $request->date,
                'reference' => $request->reference,
                'amount' => $request->amount,
                'note' => $request->note,
                'purchase_return_id' => $request->purchase_return_id,
                'payment_method' => $request->payment_method,
            ]);
        });

        session()->flash('info', trans('purchasesreturn.purchase-return-payment-updated'));

        return redirect()->route('purchase-returns.index');
    }

    public function destroy(PurchaseReturnPayment $purchaseReturnPayment)
    {
        abort_if(Gate::denies('access_purchase_return_payments'), 403);

        $purchaseReturnPayment->delete();

        session()->flash('warning', trans('purchasesreturn.purchase-return-payment-deleted'));

        return redirect()->route('purchase-returns.index');
    }
}
