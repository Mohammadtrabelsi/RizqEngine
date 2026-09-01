<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\StoreSaleReturnRequest;
use App\Http\Requests\UpdateSaleReturnRequest;
use App\Models\SaleReturn;
use App\Services\SaleReturnService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class SalesReturnController extends Controller
{
    public function __construct(private readonly SaleReturnService $saleReturns) {}

    public function index()
    {
        abort_if(Gate::denies('access_sale_returns'), 403);

        return view('salesreturn.index');

    }

    public function create()
    {
        abort_if(Gate::denies('create_sale_returns'), 403);

        Cart::instance('sale_return')->destroy();

        return view('salesreturn.create');
    }

    public function store(StoreSaleReturnRequest $request)
    {
        abort_if(Gate::denies('create_sale_returns'), 403);

        try {
            $this->saleReturns->createSaleReturn($request->all());
        } catch (InsufficientStockException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        session()->flash('success', trans('salesreturn.sale-return-created'));

        return redirect()->route('sale-returns.index');
    }

    public function show(SaleReturn $sale_return)
    {
        abort_if(Gate::denies('show_sale_returns'), 403);

        $customer = $this->saleReturns->customerFor($sale_return);

        return view('salesreturn.show', compact('sale_return', 'customer'));
    }

    public function edit(SaleReturn $sale_return)
    {
        abort_if(Gate::denies('edit_sale_returns'), 403);

        $this->saleReturns->loadCart($sale_return);

        return view('salesreturn.edit', compact('sale_return'));
    }

    public function update(UpdateSaleReturnRequest $request, SaleReturn $sale_return)
    {
        abort_if(Gate::denies('edit_sale_returns'), 403);

        try {
            $this->saleReturns->updateSaleReturn($sale_return, $request->all());
        } catch (InsufficientStockException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        session()->flash('info', trans('salesreturn.sale-return-updated'));

        return redirect()->route('sale-returns.index');
    }

    public function destroy(SaleReturn $sale_return)
    {
        abort_if(Gate::denies('delete_sale_returns'), 403);

        $this->saleReturns->delete($sale_return);

        session()->flash('warning', trans('salesreturn.sale-return-deleted'));

        return redirect()->route('sale-returns.index');
    }
}
