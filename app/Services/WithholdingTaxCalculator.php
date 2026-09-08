<?php

namespace App\Services;

use App\Enums\WithholdingCalculationBase;
use App\Models\WithholdingTax;
use Illuminate\Support\Collection;

/**
 * The withholding-tax (retenue à la source) calculation engine.
 *
 * This is the single source of truth for how a RAS is computed: given a
 * document's HT / TVA / TTC figures and the withholding taxes applied to it,
 * it returns one line per withholding with its taxable base and withheld
 * amount, the total withheld, and the resulting net payable. Both the Livewire
 * selector (live preview) and {@see PurchaseService} (persistence) call this so
 * the number the user sees is exactly the number that is stored.
 *
 * All monetary rounding happens here and nowhere else — never in Blade or
 * JavaScript. Amounts are rounded to {@see self::SCALE} decimals (millimes),
 * matching the Tunisian dinar's three-decimal precision used on certificates.
 */
class WithholdingTaxCalculator
{
    /** Decimal places money is rounded to (Tunisian dinar = 3, millimes). */
    public const SCALE = 3;

    /**
     * Compute the withholding breakdown for a document.
     *
     * @param  iterable<WithholdingTax>  $withholdingTaxes  The taxes to apply.
     * @param  float  $ht   Net amount excluding tax (montant HT).
     * @param  float  $tva  VAT amount (montant TVA).
     * @param  float  $ttc  Tax-inclusive total (montant TTC).
     * @return array{
     *     lines: array<int, array{withholding_tax_id: int|null, name: string, code: string, rate: float, calculation_base: string, taxable_amount: float, amount: float}>,
     *     total: float,
     *     net_payable: float,
     *     ttc: float
     * }
     */
    public function calculate(iterable $withholdingTaxes, float $ht, float $tva, float $ttc): array
    {
        $lines = [];
        $total = 0.0;

        foreach ($withholdingTaxes as $withholding) {
            $base = $withholding->calculation_base instanceof WithholdingCalculationBase
                ? $withholding->calculation_base
                : WithholdingCalculationBase::from((string) $withholding->calculation_base);

            $taxable = $this->round($base->taxableAmount($ht, $tva, $ttc));
            $amount = $this->round($taxable * ((float) $withholding->rate) / 100);

            $lines[] = [
                'withholding_tax_id' => $withholding->id ?? null,
                'name' => (string) $withholding->name,
                'code' => (string) $withholding->code,
                'rate' => (float) $withholding->rate,
                'calculation_base' => $base->value,
                'taxable_amount' => $taxable,
                'amount' => $amount,
            ];

            $total += $amount;
        }

        $total = $this->round($total);

        return [
            'lines' => $lines,
            'total' => $total,
            'net_payable' => $this->round($ttc - $total),
            'ttc' => $this->round($ttc),
        ];
    }

    /**
     * Load the given withholding taxes by id (preserving no particular order)
     * and compute the breakdown. Ids that do not resolve are ignored.
     *
     * @param  array<int, int|string>  $ids
     */
    public function calculateForIds(array $ids, float $ht, float $tva, float $ttc): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if (empty($ids)) {
            return $this->calculate([], $ht, $tva, $ttc);
        }

        /** @var Collection<int, WithholdingTax> $taxes */
        $taxes = WithholdingTax::whereIn('id', $ids)->get();

        return $this->calculate($taxes, $ht, $tva, $ttc);
    }

    /**
     * Round a monetary value to the app's withholding precision.
     */
    public function round(float $value): float
    {
        return round($value, self::SCALE);
    }
}
