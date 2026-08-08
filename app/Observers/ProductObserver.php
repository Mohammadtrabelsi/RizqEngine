<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockMovementContext;

/**
 * Records an immutable stock-ledger entry whenever a product's on-hand
 * quantity changes, no matter which module triggered the change (sale,
 * purchase, adjustment, return or a manual stock operation).
 */
class ProductObserver
{
    /**
     * Log the opening balance when a product is first created with stock.
     */
    public function created(Product $product): void
    {
        $context = StockMovementContext::pull();
        $quantity = (int) $product->product_quantity;

        if ($quantity === 0) {
            return;
        }

        $this->record($product, [
            'type' => $context['type'] ?? 'opening',
            'quantity' => $quantity,
            'quantity_before' => 0,
            'quantity_after' => $quantity,
            'reference_type' => $context['reference_type'] ?? null,
            'reference_id' => $context['reference_id'] ?? null,
            'note' => $context['note'] ?? 'Opening stock',
        ]);
    }

    /**
     * Log an in/out movement whenever the quantity actually changes.
     */
    public function updated(Product $product): void
    {
        // Always consume the context so it can never leak to a later update,
        // even when this particular update did not touch the quantity.
        $context = StockMovementContext::pull();

        if (! $product->wasChanged('product_quantity')) {
            return;
        }

        $before = (int) $product->getOriginal('product_quantity');
        $after = (int) $product->product_quantity;
        $delta = $after - $before;

        if ($delta === 0) {
            return;
        }

        $this->record($product, [
            'type' => $context['type'] ?? ($delta > 0 ? 'in' : 'out'),
            'quantity' => abs($delta),
            'quantity_before' => $before,
            'quantity_after' => $after,
            'reference_type' => $context['reference_type'] ?? null,
            'reference_id' => $context['reference_id'] ?? null,
            'note' => $context['note'] ?? null,
        ]);
    }

    protected function record(Product $product, array $attributes): void
    {
        StockMovement::create(array_merge($attributes, [
            'product_id' => $product->id,
            'user_id' => auth()->id(),
        ]));
    }
}
