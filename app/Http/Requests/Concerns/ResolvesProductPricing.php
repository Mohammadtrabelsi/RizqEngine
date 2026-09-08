<?php

namespace App\Http\Requests\Concerns;

/**
 * Shared pricing resolution for the product create/edit forms.
 *
 * A product's sale price can be entered in one of two ways: directly, or by
 * giving the desired margin (a percentage markup on the cost). When the margin
 * mode is used, the sale price is derived here — server-side, so the stored
 * price never depends on the browser's calculation — before validation runs.
 */
trait ResolvesProductPricing
{
    /**
     * Validation rules for the pricing-mode selector and margin field. Merge
     * these into the request's own rules().
     *
     * @return array<string, array<int, string>>
     */
    protected function pricingRules(): array
    {
        return [
            'pricing_mode' => ['nullable', 'in:price,margin'],
            'product_margin' => ['nullable', 'numeric', 'min:0', 'max:1000000', 'required_if:pricing_mode,margin'],
        ];
    }

    /**
     * When the margin mode is selected, compute product_price from the cost and
     * margin so the rest of validation (and persistence) sees a concrete price.
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('pricing_mode') !== 'margin') {
            return;
        }

        $cost = (float) $this->input('product_cost');
        $margin = $this->input('product_margin');

        if ($cost <= 0 || ! is_numeric($margin)) {
            return;
        }

        $this->merge([
            'product_price' => round($cost * (1 + ((float) $margin) / 100), 2),
        ]);
    }
}
