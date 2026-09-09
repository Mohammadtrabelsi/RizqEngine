<?php

namespace App\Models;

use App\Enums\WithholdingCalculationBase;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single withholding tax (retenue à la source) applied to a purchase,
 * stored as an immutable snapshot of the fiscal figures used at invoice time.
 *
 * The name/code/rate/base are copied from the {@see WithholdingTax} so a later
 * edit of the master data never rewrites this historical line. Monetary
 * amounts follow the app-wide convention of being persisted in centimes
 * (× 100) and exposed as decimals through accessors.
 *
 * @property int $id
 * @property int $purchase_id
 * @property int|null $withholding_tax_id
 * @property string $name
 * @property string $code
 * @property float $rate
 * @property WithholdingCalculationBase $calculation_base
 * @property float $taxable_amount
 * @property float $amount
 */
class PurchaseWithholdingTax extends Model
{
    use HasFactory, TracksUserActions;

    protected $guarded = [];

    protected $casts = [
        'rate' => 'float',
        'calculation_base' => WithholdingCalculationBase::class,
    ];

    /**
     * @return BelongsTo<Purchase, $this>
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * The master withholding tax this line snapshotted (may be null if the
     * master record was later deleted; the snapshot fields still stand).
     *
     * @return BelongsTo<WithholdingTax, $this>
     */
    public function withholdingTax(): BelongsTo
    {
        return $this->belongsTo(WithholdingTax::class);
    }

    public function getTaxableAmountAttribute($value): float
    {
        return $value / 100;
    }

    public function getAmountAttribute($value): float
    {
        return $value / 100;
    }
}
