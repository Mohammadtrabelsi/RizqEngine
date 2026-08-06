<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Product\DataTables\ProductCategoriesDataTable;
use Modules\Product\Entities\Category;

class CategoriesController extends Controller
{
    public function index(ProductCategoriesDataTable $dataTable)
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        return $dataTable->render('product::categories.index');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        $request->validate([
            'category_code' => 'required|unique:categories,category_code',
            'category_name' => 'required',
        ]);

        Category::create([
            'category_code' => $request->category_code,
            'category_name' => $request->category_name,
        ]);

        session()->flash('success', trans('product.product-category-created'));

        return redirect()->back();
    }

    public function edit($id)
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        $category = Category::findOrFail($id);

        return view('product::categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        $request->validate([
            'category_code' => 'required|unique:categories,category_code,'.$id,
            'category_name' => 'required',
        ]);

        Category::findOrFail($id)->update([
            'category_code' => $request->category_code,
            'category_name' => $request->category_name,
        ]);

        session()->flash('info', trans('product.product-category-updated'));

        return redirect()->route('product-categories.index');
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        $category = Category::findOrFail($id);

        if ($category->products()->exists()) {
            return back()->withErrors('Can\'t delete because there are products associated with this category.');
        }

        $category->delete();

        session()->flash('warning', trans('product.product-category-deleted'));

        return redirect()->route('product-categories.index');
    }
}
