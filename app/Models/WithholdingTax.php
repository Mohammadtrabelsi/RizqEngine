<?php

namespace App\Models;

use App\Enums\WithholdingCalculationBase;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * A configurable withholding tax (retenue à la source / RAS) as used in
 * Tunisian taxation. Unlike {@see Tax} (VAT and stamp-like taxes that build
 * up the TTC), a withholding tax is deducted *after* the TTC to produce the
 * net actually paid to the beneficiary:
 *
 *     Net à payer = TTC − Σ(retenues)
 *
 * Rates are never hardcoded in components: administrators manage them here and
 * every applied line keeps a snapshot (see {@see PurchaseWithholdingTax}) so
 * changing a rate later never alters historical documents.
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property float $rate Percentage rate (0–100) applied to the calculation base.
 * @property string $calculation_base One of {@see WithholdingCalculationBase}.
 * @property bool $active
 * @property bool $applicable_to_purchases
 * @property bool $applicable_to_sales
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 */
class WithholdingTax extends Model
{
    use HasFactory, TracksUserActions;

    protected $guarded = [];

    protected $casts = [
        'rate' => 'float',
        'active' => 'boolean',
        'applicable_to_purchases' => 'boolean',
        'applicable_to_sales' => 'boolean',
        'calculation_base' => WithholdingCalculationBase::class,
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Only withholding taxes that are active and, when validity dates are set,
     * effective on the given date (defaults to today).
     *
     * @param  Builder<WithholdingTax>  $query
     * @return Builder<WithholdingTax>
     */
    public function scopeEffective(Builder $query, ?Carbon $on = null): Builder
    {
        $on ??= Carbon::today();

        return $query->where('active', true)
            ->where(function (Builder $q) use ($on) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $on);
            })
            ->where(function (Builder $q) use ($on) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $on);
            });
    }

    /**
     * Constrain to the withholding taxes selectable on a given document side.
     *
     * @param  Builder<WithholdingTax>  $query
     * @return Builder<WithholdingTax>
     */
    public function scopeForSide(Builder $query, string $side): Builder
    {
        return match ($side) {
            'sale' => $query->where('applicable_to_sales', true),
            default => $query->where('applicable_to_purchases', true),
        };
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<PurchaseWithholdingTax, $this>
     */
    public function purchaseLines()
    {
        return $this->hasMany(PurchaseWithholdingTax::class);
    }

    /**
     * Whether any document has already applied this withholding tax, in which
     * case it must be deactivated rather than deleted to preserve history.
     */
    public function isUsed(): bool
    {
        return $this->purchaseLines()->exists();
    }
}
