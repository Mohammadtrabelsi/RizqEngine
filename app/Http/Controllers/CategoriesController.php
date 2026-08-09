<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class CategoriesController extends Controller
{
    public function __construct(private readonly CategoryService $categories) {}

    public function index()
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        return view('product.categories.index');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        $data = $request->validate([
            'category_code' => 'required|unique:categories,category_code',
            'category_name' => 'required',
        ]);

        $this->categories->create($data);

        session()->flash('success', trans('product.product-category-created'));

        return redirect()->back();
    }

    public function edit($id)
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        $category = $this->categories->findOrFail($id);

        return view('product.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        $data = $request->validate([
            'category_code' => 'required|unique:categories,category_code,'.$id,
            'category_name' => 'required',
        ]);

        $this->categories->update((int) $id, $data);

        session()->flash('info', trans('product.product-category-updated'));

        return redirect()->route('product-categories.index');
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        if (! $this->categories->delete((int) $id)) {
            return back()->withErrors('Can\'t delete because there are products associated with this category.');
        }

        session()->flash('warning', trans('product.product-category-deleted'));

        return redirect()->route('product-categories.index');
    }
}
