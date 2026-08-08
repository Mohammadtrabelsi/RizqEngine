<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\Facades\DB;

/**
 * Central place for programmatic stock changes.
 *
 * Every write runs inside a database transaction and mutates the product
 * through the model, so the {@see ProductObserver}
 * records a matching ledger entry. Callers should use this service instead of
 * touching `product_quantity` by hand whenever they need attribution and the
 * negative-stock guard.
 */
class StockService
{
    /**
     * Add stock to a product and record an "in" movement.
     */
    public function stockIn(Product $product, int $quantity, ?string $note = null, ?string $referenceType = 'Manual', ?int $referenceId = null): Product
    {
        $this->guardPositive($quantity);

        return DB::transaction(function () use ($product, $quantity, $note, $referenceType, $referenceId) {
            $product = Product::lockForUpdate()->findOrFail($product->id);

            StockMovementContext::set('in', $referenceType, $referenceId, $note);
            $product->update(['product_quantity' => $product->product_quantity + $quantity]);

            return $product;
        });
    }

    /**
     * Remove stock from a product and record an "out" movement.
     *
     * @throws InsufficientStockException when the product does not hold enough stock.
     */
    public function stockOut(Product $product, int $quantity, ?string $note = null, ?string $referenceType = 'Manual', ?int $referenceId = null): Product
    {
        $this->guardPositive($quantity);

        return DB::transaction(function () use ($product, $quantity, $note, $referenceType, $referenceId) {
            $product = Product::lockForUpdate()->findOrFail($product->id);

            if ($product->product_quantity < $quantity) {
                throw new InsufficientStockException(
                    "Cannot remove {$quantity} unit(s) from \"{$product->product_name}\"; only {$product->product_quantity} in stock."
                );
            }

            StockMovementContext::set('out', $referenceType, $referenceId, $note);
            $product->update(['product_quantity' => $product->product_quantity - $quantity]);

            return $product;
        });
    }

    /**
     * Set a product's on-hand quantity to an absolute value, recording the
     * resulting increase/decrease as an adjustment.
     */
    public function setQuantity(Product $product, int $newQuantity, ?string $note = null): Product
    {
        if ($newQuantity < 0) {
            throw new InsufficientStockException('Stock quantity cannot be negative.');
        }

        return DB::transaction(function () use ($product, $newQuantity, $note) {
            $product = Product::lockForUpdate()->findOrFail($product->id);

            if ($product->product_quantity === $newQuantity) {
                return $product;
            }

            StockMovementContext::set('adjustment', 'Adjustment', null, $note);
            $product->update(['product_quantity' => $newQuantity]);

            return $product;
        });
    }

    protected function guardPositive(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Stock movement quantity must be greater than zero.');
        }
    }
}
