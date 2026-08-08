<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Models\SaleReturn;
use App\Models\SaleReturnPayment;

class SaleReturnPaymentsController extends Controller
{
    public function index($sale_return_id)
    {
        abort_if(Gate::denies('access_sale_return_payments'), 403);

        $sale_return = SaleReturn::findOrFail($sale_return_id);

        $payments = SaleReturnPayment::where('sale_return_id', $sale_return_id)->latest()->paginate(12);

        return view('salesreturn.payments.index', compact('sale_return', 'payments'));
    }

    public function create($sale_return_id)
    {
        abort_if(Gate::denies('access_sale_return_payments'), 403);

        $sale_return = SaleReturn::findOrFail($sale_return_id);

        return view('salesreturn.payments.create', compact('sale_return'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('access_sale_return_payments'), 403);

        $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'note' => 'nullable|string|max:1000',
            'sale_return_id' => 'required',
            'payment_method' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            SaleReturnPayment::create([
                'date' => $request->date,
                'reference' => $request->reference,
                'amount' => $request->amount,
                'note' => $request->note,
                'sale_return_id' => $request->sale_return_id,
                'payment_method' => $request->payment_method,
            ]);

            $sale_return = SaleReturn::findOrFail($request->sale_return_id);

            $due_amount = $sale_return->due_amount - $request->amount;

            if ($due_amount == $sale_return->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $sale_return->update([
                'paid_amount' => ($sale_return->paid_amount + $request->amount) * 100,
                'due_amount' => $due_amount * 100,
                'payment_status' => $payment_status,
            ]);
        });

        session()->flash('success', trans('salesreturn.sale-return-payment-created'));

        return redirect()->route('sale-returns.index');
    }

    public function edit($sale_return_id, SaleReturnPayment $saleReturnPayment)
    {
        abort_if(Gate::denies('access_sale_return_payments'), 403);

        $sale_return = SaleReturn::findOrFail($sale_return_id);

        return view('salesreturn.payments.edit', compact('saleReturnPayment', 'sale_return'));
    }

    public function update(Request $request, SaleReturnPayment $saleReturnPayment)
    {
        abort_if(Gate::denies('access_sale_return_payments'), 403);

        $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'note' => 'nullable|string|max:1000',
            'sale_return_id' => 'required',
            'payment_method' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $saleReturnPayment) {
            $sale_return = $saleReturnPayment->saleReturn;

            $due_amount = ($sale_return->due_amount + $saleReturnPayment->amount) - $request->amount;

            if ($due_amount == $sale_return->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $sale_return->update([
                'paid_amount' => (($sale_return->paid_amount - $saleReturnPayment->amount) + $request->amount) * 100,
                'due_amount' => $due_amount * 100,
                'payment_status' => $payment_status,
            ]);

            $saleReturnPayment->update([
                'date' => $request->date,
                'reference' => $request->reference,
                'amount' => $request->amount,
                'note' => $request->note,
                'sale_return_id' => $request->sale_return_id,
                'payment_method' => $request->payment_method,
            ]);
        });

        session()->flash('info', trans('salesreturn.sale-return-payment-updated'));

        return redirect()->route('sale-returns.index');
    }

    public function destroy(SaleReturnPayment $saleReturnPayment)
    {
        abort_if(Gate::denies('access_sale_return_payments'), 403);

        $saleReturnPayment->delete();

        session()->flash('warning', trans('salesreturn.sale-return-payment-deleted'));

        return redirect()->route('sale-returns.index');
    }
}
