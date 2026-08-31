<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Sale;
use App\Services\SaleService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class SaleController extends Controller
{
    public function __construct(private readonly SaleService $sales) {}

    public function index()
    {
        abort_if(Gate::denies('access_sales'), 403);

        return view('sale.index');

    }

    public function create()
    {
        abort_if(Gate::denies('create_sales'), 403);

        Cart::instance('sale')->destroy();

        return view('sale.create');
    }

    public function store(StoreSaleRequest $request)
    {
        abort_if(Gate::denies('create_sales'), 403);

        try {
            $this->sales->createSale($request->all());
        } catch (InsufficientStockException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        session()->flash('success', trans('sale.sale-created'));

        return redirect()->route('sales.index');
    }

    public function show(Sale $sale)
    {
        abort_if(Gate::denies('show_sales'), 403);

        $customer = $this->sales->customerFor($sale);

        return view('sale.show', compact('sale', 'customer'));
    }

    public function edit(Sale $sale)
    {
        abort_if(Gate::denies('edit_sales'), 403);

        $this->sales->loadCart($sale);

        return view('sale.edit', compact('sale'));
    }

    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        abort_if(Gate::denies('edit_sales'), 403);

        try {
            $this->sales->updateSale($sale, $request->all());
        } catch (InsufficientStockException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        session()->flash('info', trans('sale.sale-updated'));

        return redirect()->route('sales.index');
    }

    public function destroy(Sale $sale)
    {
        abort_if(Gate::denies('delete_sales'), 403);

        $this->sales->delete($sale);

        session()->flash('warning', trans('sale.sale-deleted'));

        return redirect()->route('sales.index');
    }
}
