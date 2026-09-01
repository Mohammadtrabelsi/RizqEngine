<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $products) {}

    public function index()
    {
        abort_if(Gate::denies('access_products'), 403);

        return view('product.products.index');

    }

    public function create(CategoryService $categories)
    {
        abort_if(Gate::denies('create_products'), 403);

        $category_code = $categories->nextCode();

        return view('product.products.create', compact('category_code'));
    }

    public function store(StoreProductRequest $request)
    {
        $this->products->create($request->except('document'), $request->input('document', []));

        session()->flash('success', trans('product.product-created'));

        return redirect()->route('products.index');
    }

    public function show(Product $product)
    {
        abort_if(Gate::denies('show_products'), 403);

        $transactions = $this->products->transactionsFor($product);
        $orders = $this->products->ordersFor($product);

        return view('product.products.show', compact('product', 'transactions', 'orders'));
    }

    public function edit(Product $product)
    {
        abort_if(Gate::denies('edit_products'), 403);

        return view('product.products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->products->update(
            $product,
            $request->except('document'),
            $request->has('document') ? $request->input('document', []) : null,
        );

        session()->flash('info', trans('product.product-updated'));

        return redirect()->route('products.index');
    }

    public function destroy(Product $product)
    {
        abort_if(Gate::denies('delete_products'), 403);

        $this->products->delete($product);

        session()->flash('warning', trans('product.product-deleted'));

        return redirect()->route('products.index');
    }
}
