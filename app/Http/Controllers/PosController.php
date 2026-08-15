<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\StorePosSaleRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Services\SaleService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Routing\Controller;

class PosController extends Controller
{
    public function __construct(private readonly SaleService $sales) {}

    public function index()
    {
        Cart::instance('sale')->destroy();

        $customers = Customer::all();
        $product_categories = Category::all();

        return view('sale.pos.index', compact('product_categories', 'customers'));
    }

    public function store(StorePosSaleRequest $request)
    {
        try {
            $this->sales->createPosSale($request->all());
        } catch (InsufficientStockException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        session()->flash('success', trans('sale.sale-created'));

        return redirect()->route('sales.index');
    }
}
