<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\StorePosSaleRequest;
use App\Services\CategoryService;
use App\Services\CustomerService;
use App\Services\SaleService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Routing\Controller;

class PosController extends Controller
{
    public function __construct(private readonly SaleService $sales) {}

    public function index(CustomerService $customers, CategoryService $categories)
    {
        Cart::instance('sale')->destroy();

        $customers = $customers->all();
        $product_categories = $categories->all();

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
