<?php

namespace App\Http\Controllers;

use App\Models\SaleReturn;
use App\Models\SaleReturnPayment;
use App\Services\SaleReturnService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class SaleReturnPaymentsController extends Controller
{
    public function __construct(private readonly SaleReturnService $saleReturns) {}

    public function index($sale_return_id)
    {
        abort_if(Gate::denies('access_sale_return_payments'), 403);

        $sale_return = SaleReturn::findOrFail($sale_return_id);

        return view('salesreturn.payments.index', compact('sale_return'));

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

        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'note' => 'nullable|string|max:1000',
            'sale_return_id' => 'required',
            'payment_method' => 'required|string|max:255',
        ]);

        $this->saleReturns->addPayment($data);

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

        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'note' => 'nullable|string|max:1000',
            'sale_return_id' => 'required',
            'payment_method' => 'required|string|max:255',
        ]);

        $this->saleReturns->updatePayment($saleReturnPayment, $data);

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
