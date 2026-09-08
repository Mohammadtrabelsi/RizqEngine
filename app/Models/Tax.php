<?php

namespace App\Models;

use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A named tax (e.g. "VAT 19%" or a flat stamp duty) that can be applied,
 * alone or combined with others, to a product or to a purchase/sale document.
 *
 * @property int $id
 * @property string $name
 * @property string $type "percentage" (rate is a %) or "fixed" (rate is a flat amount)
 * @property float $rate
 * @property string $apply_to Where the tax may be selected: "product", "purchase", "sale" or the legacy "order" (both sides)
 * @property int $order Position taxes are applied/listed in when combined
 */
class Tax extends Model
{
    use HasFactory, TracksUserActions;

    const TYPE_PERCENTAGE = 'percentage';

    const TYPE_FIXED = 'fixed';

    /** Selectable directly on a product. */
    const APPLY_TO_PRODUCT = 'product';

    /** Legacy scope: selectable on any purchase- or sale-side document. */
    const APPLY_TO_ORDER = 'order';

    /** Selectable on purchase-side documents (supplier orders / achats). */
    const APPLY_TO_PURCHASE = 'purchase';

    /** Selectable on sale-side documents (customer orders / ventes). */
    const APPLY_TO_SALE = 'sale';

    /** Cart instances that belong to the purchase (achat) side of the ledger. */
    const PURCHASE_CART_INSTANCES = ['purchase', 'purchase_return', 'bon_commande'];

    /** Cart instances that belong to the sale (vente) side of the ledger. */
    const SALE_CART_INSTANCES = ['sale', 'sale_return', 'quotation', 'commande'];

    protected $guarded = [];

    protected $casts = [
        'rate' => 'float',
        'order' => 'integer',
    ];

    /**
     * The apply_to scopes whose taxes may be selected on the given cart
     * instance's document. Purchase- and sale-side documents each see their
     * own scope plus the legacy "order" scope (which spans both sides);
     * anything unrecognised falls back to the legacy scope only.
     *
     * @return array<int, string>
     */
    public static function applyScopesForCartInstance(string $cartInstance): array
    {
        if (in_array($cartInstance, self::PURCHASE_CART_INSTANCES, true)) {
            return [self::APPLY_TO_PURCHASE, self::APPLY_TO_ORDER];
        }

        if (in_array($cartInstance, self::SALE_CART_INSTANCES, true)) {
            return [self::APPLY_TO_SALE, self::APPLY_TO_ORDER];
        }

        return [self::APPLY_TO_ORDER];
    }

    /**
     * Constrain a query to the taxes selectable on the given cart instance's
     * document (its own side plus the legacy "order" scope).
     *
     * @param  Builder<Tax>  $query
     * @return Builder<Tax>
     */
    public function scopeForCartInstance(Builder $query, string $cartInstance): Builder
    {
        return $query->whereIn('apply_to', self::applyScopesForCartInstance($cartInstance));
    }

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
