<?php

namespace App\Http\Controllers;

use App\Models\SalePayment;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class SalePaymentsController extends Controller
{
    public function __construct(private readonly SaleService $sales) {}

    public function index($sale_id)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        $sale = $this->sales->findOrFail($sale_id);

        return view('sale.payments.index', compact('sale'));

    }

    public function create($sale_id)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        $sale = $this->sales->findOrFail($sale_id);

        return view('sale.payments.create', compact('sale'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:1000',
            'sale_id' => 'required',
            'payment_method' => 'required|string|max:255',
        ]);

        $this->sales->addPayment($data);

        session()->flash('success', trans('sale.sale-payment-created'));

        return redirect()->route('sales.index');
    }

    public function edit($sale_id, SalePayment $salePayment)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        $sale = $this->sales->findOrFail($sale_id);

        return view('sale.payments.edit', compact('salePayment', 'sale'));
    }

    public function update(Request $request, SalePayment $salePayment)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:1000',
            'sale_id' => 'required',
            'payment_method' => 'required|string|max:255',
        ]);

        $this->sales->updatePayment($salePayment, $data);

        session()->flash('info', trans('sale.sale-payment-updated'));

        return redirect()->route('sales.index');
    }

    public function destroy(SalePayment $salePayment)
    {
        abort_if(Gate::denies('access_sale_payments'), 403);

        $this->sales->deletePayment($salePayment->id);

        session()->flash('warning', trans('sale.sale-payment-deleted'));

        return redirect()->route('sales.index');
    }
}
