<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\PurchaseReturn;
use App\Models\SaleReturn;
use App\Services\CategoryService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
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

        $transactions = $this->productTransactions($product);
        $orders = $this->productOrders($product);

        return view('product.products.show', compact('product', 'transactions', 'orders'));
    }

    /**
     * Build a date-sorted list of every order document (Bon de Commande and
     * Commande) whose line items reference the given product.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function productOrders(Product $product): Collection
    {
        $orders = collect();

        $product->bonCommandeDetails()->with('bonCommande.customer')->get()
            ->each(function ($detail) use ($orders) {
                $parent = $detail->bonCommande;

                if (! $parent) {
                    return;
                }

                $orders->push([
                    'type' => 'bon_commande',
                    'label' => 'Bon de Commande',
                    'badge' => 'info',
                    'reference' => $parent->reference,
                    'party' => optional($parent->customer)->customer_name,
                    'status' => $parent->status,
                    'route' => route('bon-commandes.show', $parent->id),
                    'quantity' => $detail->quantity,
                    'date' => $parent->created_at ?? $detail->created_at,
                ]);
            });

        $product->commandeDetails()->with('commande.customer')->get()
            ->each(function ($detail) use ($orders) {
                $parent = $detail->commande;

                if (! $parent) {
                    return;
                }

                $orders->push([
                    'type' => 'commande',
                    'label' => 'Commande',
                    'badge' => 'primary',
                    'reference' => $parent->reference,
                    'party' => optional($parent->customer)->customer_name,
                    'status' => $parent->status,
                    'route' => route('commandes.show', $parent->id),
                    'quantity' => $detail->quantity,
                    'date' => $parent->created_at ?? $detail->created_at,
                ]);
            });

        return $orders->sortByDesc('date')->values();
    }

    /**
     * Build a unified, date-sorted list of every transaction (sales,
     * purchases, and their returns) that involved the given product.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function productTransactions(Product $product): Collection
    {
        $transactions = collect();

        $product->saleDetails()->with('sale')->get()
            ->each(function ($detail) use ($transactions) {
                $parent = $detail->sale;

                $transactions->push([
                    'type' => 'sale',
                    'label' => 'Sale',
                    'badge' => 'success',
                    'reference' => $parent?->reference,
                    'party' => $parent?->customer_name,
                    'route' => $parent ? route('sales.show', $parent->id) : null,
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->unit_price,
                    'sub_total' => $detail->sub_total,
                    'date' => $parent->created_at ?? $detail->created_at,
                ]);
            });

        $product->purchaseDetails()->with('purchase')->get()
            ->each(function ($detail) use ($transactions) {
                $parent = $detail->purchase;

                $transactions->push([
                    'type' => 'purchase',
                    'label' => 'Purchase',
                    'badge' => 'primary',
                    'reference' => $parent?->reference,
                    'party' => $parent?->supplier_name,
                    'route' => $parent ? route('purchases.show', $parent->id) : null,
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->unit_price,
                    'sub_total' => $detail->sub_total,
                    'date' => $parent->created_at ?? $detail->created_at,
                ]);
            });

        $saleReturns = SaleReturn::whereIn(
            'id',
            $product->saleReturnDetails()->pluck('sale_return_id')
        )->get()->keyBy('id');

        $product->saleReturnDetails()->get()
            ->each(function ($detail) use ($transactions, $saleReturns) {
                $parent = $saleReturns->get($detail->sale_return_id);

                $transactions->push([
                    'type' => 'sale_return',
                    'label' => 'Sale Return',
                    'badge' => 'warning',
                    'reference' => $parent?->reference,
                    'party' => $parent?->customer_name,
                    'route' => $parent ? route('sale-returns.show', $parent->id) : null,
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->unit_price,
                    'sub_total' => $detail->sub_total,
                    'date' => $parent->created_at ?? $detail->created_at,
                ]);
            });

        $purchaseReturns = PurchaseReturn::whereIn(
            'id',
            $product->purchaseReturnDetails()->pluck('purchase_return_id')
        )->get()->keyBy('id');

        $product->purchaseReturnDetails()->get()
            ->each(function ($detail) use ($transactions, $purchaseReturns) {
                $parent = $purchaseReturns->get($detail->purchase_return_id);

                $transactions->push([
                    'type' => 'purchase_return',
                    'label' => 'Purchase Return',
                    'badge' => 'danger',
                    'reference' => $parent?->reference,
                    'party' => $parent?->supplier_name,
                    'route' => $parent ? route('purchase-returns.show', $parent->id) : null,
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->unit_price,
                    'sub_total' => $detail->sub_total,
                    'date' => $parent->created_at ?? $detail->created_at,
                ]);
            });

        return $transactions->sortByDesc('date')->values();
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
