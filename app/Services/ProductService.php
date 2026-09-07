<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseReturn;
use App\Models\SaleReturn;
use App\Models\Tax;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Owns product persistence (including the Dropzone image media collection) and
 * the aggregation of a product's transaction/order history for its detail page.
 */
class ProductService
{
    /**
     * Create a product and attach any Dropzone-staged images.
     *
     * @param  array<string, mixed>  $attributes  Product columns (already excluding "document").
     * @param  array<int, string>  $documents  Dropzone temp file names.
     */
    public function create(array $attributes, array $documents = []): Product
    {
        $taxIds = $this->extractTaxIds($attributes);

        $product = Product::create($attributes);
        $product->taxes()->sync($taxIds);

        foreach ($documents as $file) {
            $product->addMedia(Storage::path('temp/dropzone/'.$file))->toMediaCollection('images');
        }

        return $product;
    }

    /**
     * Update a product and reconcile its image media with the submitted set of
     * Dropzone file names.
     *
     * @param  array<string, mixed>  $attributes  Product columns (already excluding "document").
     * @param  array<int, string>|null  $documents  Dropzone temp file names that should
     *                                              remain/attach, or null to leave media untouched.
     */
    public function update(Product $product, array $attributes, ?array $documents = null): Product
    {
        $taxIds = $this->extractTaxIds($attributes);

        $product->update($attributes);
        $product->taxes()->sync($taxIds);

        if ($documents === null) {
            return $product;
        }

        foreach ($product->getMedia('images') as $media) {
            if (! in_array($media->file_name, $documents)) {
                $media->delete();
            }
        }

        $existing = $product->getMedia('images')->pluck('file_name')->toArray();

        foreach ($documents as $file) {
            if (count($existing) === 0 || ! in_array($file, $existing)) {
                $product->addMedia(Storage::path('temp/dropzone/'.$file))->toMediaCollection('images');
            }
        }

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    /**
     * Pull the submitted "product_taxes" tax IDs out of the attribute set
     * (it isn't a products column) so they can be synced to the product's
     * taxes pivot separately. When any are selected, product_order_tax is
     * recomputed server-side from their percentage-type rates so a tampered
     * client value can't override it; with none selected the submitted
     * product_order_tax (manual entry, e.g. on products created before this
     * feature existed) is left untouched.
     *
     * @param  array<string, mixed>  &$attributes
     * @return array<int, int>
     */
    private function extractTaxIds(array &$attributes): array
    {
        $taxIds = array_map('intval', $attributes['product_taxes'] ?? []);
        unset($attributes['product_taxes']);

        if (! empty($taxIds)) {
            $attributes['product_order_tax'] = (int) Tax::whereIn('id', $taxIds)
                ->where('type', Tax::TYPE_PERCENTAGE)
                ->sum('rate');
        }

        return $taxIds;
    }

    /**
     * A unified, date-sorted list of every transaction (sales, purchases and
     * their returns) that involved the given product.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function transactionsFor(Product $product): Collection
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

    /**
     * A date-sorted list of every order document (Bon de Commande and Commande)
     * whose line items reference the given product.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function ordersFor(Product $product): Collection
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
}
