<?php

namespace App\Services;

/**
 * Computes the tax-adjusted line pricing for a product added to a cart.
 *
 * This is the pricing rule (inclusive / exclusive / no tax) that was
 * duplicated inside the ProductCart and POS Checkout Livewire components.
 * Callers keep responsibility for choosing the base price (sale price, cost
 * price or a manual override); this service turns a base price into the
 * price / unit_price / tax / sub_total breakdown.
 */
class CartPricingService
{
    /**
     * @param  array<string, mixed>  $product  Must expose product_tax_type and product_order_tax.
     * @param  float|int|null  $basePrice       Base price to tax; defaults to the product sale price.
     * @return array{price: float|int, unit_price: float|int, product_tax: float|int, sub_total: float|int}
     */
    public function calculate(array $product, float|int|null $basePrice = null): array
    {
        $product_price = $basePrice ?? $product['product_price'];
        $tax_rate = $product['product_order_tax'] / 100;

        if ($product['product_tax_type'] == 1) {
            // Tax exclusive: tax is added on top of the base price.
            $product_tax = $product_price * $tax_rate;

            return [
                'price' => $product_price + $product_tax,
                'unit_price' => $product_price,
                'product_tax' => $product_tax,
                'sub_total' => $product_price + $product_tax,
            ];
        }

        if ($product['product_tax_type'] == 2) {
            // Tax inclusive: the base price already contains the tax.
            $product_tax = $product_price * $tax_rate;

            return [
                'price' => $product_price,
                'unit_price' => $product_price - $product_tax,
                'product_tax' => $product_tax,
                'sub_total' => $product_price,
            ];
        }

        // No tax.
        return [
            'price' => $product_price,
            'unit_price' => $product_price,
            'product_tax' => 0.00,
            'sub_total' => $product_price,
        ];
    }
}
