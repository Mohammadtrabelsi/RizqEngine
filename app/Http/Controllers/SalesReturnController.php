<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\StoreSaleReturnRequest;
use App\Http\Requests\UpdateSaleReturnRequest;
use App\Models\Customer;
use App\Models\Product;
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

        $customer = Customer::findOrFail($sale_return->customer_id);

        return view('salesreturn.show', compact('sale_return', 'customer'));
    }

    public function edit(SaleReturn $sale_return)
    {
        abort_if(Gate::denies('edit_sale_returns'), 403);

        $sale_return_details = $sale_return->saleReturnDetails;

        Cart::instance('sale_return')->destroy();

        $cart = Cart::instance('sale_return');

        foreach ($sale_return_details as $sale_return_detail) {
            $cart->add([
                'id' => $sale_return_detail->product_id,
                'name' => $sale_return_detail->product_name,
                'qty' => $sale_return_detail->quantity,
                'price' => $sale_return_detail->price,
                'weight' => 1,
                'options' => [
                    'product_discount' => $sale_return_detail->product_discount_amount,
                    'product_discount_type' => $sale_return_detail->product_discount_type,
                    'sub_total' => $sale_return_detail->sub_total,
                    'code' => $sale_return_detail->product_code,
                    'stock' => Product::findOrFail($sale_return_detail->product_id)->product_quantity,
                    'product_tax' => $sale_return_detail->product_tax_amount,
                    'unit_price' => $sale_return_detail->unit_price,
                ],
            ]);
        }

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

        $sale_return->delete();

        session()->flash('warning', trans('salesreturn.sale-return-deleted'));

        return redirect()->route('sale-returns.index');
    }
}
