<?php

namespace App\Models;

use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A named tax (e.g. "VAT 19%" or a flat stamp duty) that can be applied,
 * alone or combined with others, to a product or to a purchase document.
 *
 * @property int $id
 * @property string $name
 * @property string $type "percentage" (rate is a %) or "fixed" (rate is a flat amount)
 * @property float $rate
 * @property string $apply_to "product" or "order"
 * @property int $order Position taxes are applied/listed in when combined
 */
class Tax extends Model
{
    use HasFactory, TracksUserActions;

    const TYPE_PERCENTAGE = 'percentage';

    const TYPE_FIXED = 'fixed';

    const APPLY_TO_PRODUCT = 'product';

    const APPLY_TO_ORDER = 'order';

    protected $guarded = [];

    protected $casts = [
        'rate' => 'float',
        'order' => 'integer',
    ];

    /**
     * Combine several percentage taxes the way they actually apply: each one
     * in turn, on the running total left by the ones before it — not summed.
     * E.g. 19% then 7% on 100 is 100 * 1.19 * 1.07 = 127.33 (a 27.33%
     * effective rate), not 100 * 1.26. Applied in `order`; multiplication
     * being commutative, the order only matters once fixed-amount taxes are
     * folded into this same sequence. Fixed-type taxes are ignored here.
     */
    public static function compoundPercentageRate(array $taxIds): float
    {
        if (empty($taxIds)) {
            return 0.0;
        }

        $multiplier = static::whereIn('id', $taxIds)
            ->where('type', self::TYPE_PERCENTAGE)
            ->orderBy('order')
            ->pluck('rate')
            ->reduce(fn (float $carry, float $rate) => $carry * (1 + $rate / 100), 1.0);

        return round(($multiplier - 1) * 100, 4);
    }
}
