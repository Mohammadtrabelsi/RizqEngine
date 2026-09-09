<?php

namespace App\Enums;

/**
 * The base amount a withholding tax (retenue à la source) is computed on.
 *
 * Tunisian withholding rules apply the rate to different bases depending on
 * the nature of the operation, so the base is configurable per withholding
 * tax rather than assumed. The values are stored as strings on the
 * withholding_taxes table so they stay stable and human-readable in the DB.
 */
enum WithholdingCalculationBase: string
{
    /** Rate applies to the net amount excluding tax (montant HT). */
    case HT = 'ht';

    /** Rate applies to the VAT amount only (montant TVA). */
    case TVA = 'tva';

    /** Rate applies to the tax-inclusive total (montant TTC). */
    case TTC = 'ttc';

    /**
     * Resolve the taxable base from a document's HT / TVA / TTC figures.
     */
    public function taxableAmount(float $ht, float $tva, float $ttc): float
    {
        return match ($this) {
            self::HT => $ht,
            self::TVA => $tva,
            self::TTC => $ttc,
        };
    }

    /**
     * Translation key for the human label of this base.
     */
    public function label(): string
    {
        return 'withholding.base_'.$this->value;
    }

    /**
     * All cases as value => label-key pairs, for building select inputs.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
