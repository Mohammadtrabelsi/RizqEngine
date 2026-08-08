<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('access_products'), 403);

        return view('product.products.index');

    }

    public function create()
    {
        abort_if(Gate::denies('create_products'), 403);

        return view('product.products.create');
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->except('document'));

        if ($request->has('document')) {
            foreach ($request->input('document', []) as $file) {
                $product->addMedia(Storage::path('temp/dropzone/'.$file))->toMediaCollection('images');
            }
        }

        session()->flash('success', trans('product.product-created'));

        return redirect()->route('products.index');
    }

    public function show(Product $product)
    {
        abort_if(Gate::denies('show_products'), 403);

        return view('product.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        abort_if(Gate::denies('edit_products'), 403);

        return view('product.products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->except('document'));

        if ($request->has('document')) {
            if (count($product->getMedia('images')) > 0) {
                foreach ($product->getMedia('images') as $media) {
                    if (! in_array($media->file_name, $request->input('document', []))) {
                        $media->delete();
                    }
                }
            }

            $media = $product->getMedia('images')->pluck('file_name')->toArray();

            foreach ($request->input('document', []) as $file) {
                if (count($media) === 0 || ! in_array($file, $media)) {
                    $product->addMedia(Storage::path('temp/dropzone/'.$file))->toMediaCollection('images');
                }
            }
        }

        session()->flash('info', trans('product.product-updated'));

        return redirect()->route('products.index');
    }

    public function destroy(Product $product)
    {
        abort_if(Gate::denies('delete_products'), 403);

        $product->delete();

        session()->flash('warning', trans('product.product-deleted'));

        return redirect()->route('products.index');
    }
}
