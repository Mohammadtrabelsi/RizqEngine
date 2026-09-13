<?php

namespace App\Models;

use App\Enums\WithholdingCalculationBase;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single withholding tax (retenue à la source) applied to a sale, stored as
 * an immutable snapshot of the fiscal figures used at invoice time.
 *
 * The name/code/rate/base are copied from the {@see WithholdingTax} so a later
 * edit of the master data never rewrites this historical line. Monetary
 * amounts are persisted in millimes (× 1000) and exposed as decimals through
 * accessors, matching {@see PurchaseWithholdingTax}.
 *
 * @property int $id
 * @property int $sale_id
 * @property int|null $withholding_tax_id
 * @property string $name
 * @property string $code
 * @property float $rate
 * @property WithholdingCalculationBase $calculation_base
 * @property float $taxable_amount
 * @property float $amount
 */
class SaleWithholdingTax extends Model
{
    use HasFactory, TracksUserActions;

    protected $guarded = [];

    protected $casts = [
        'rate' => 'float',
        'calculation_base' => WithholdingCalculationBase::class,
    ];

    /**
     * @return BelongsTo<Sale, $this>
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
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
        return $value / 1000;
    }

    public function getAmountAttribute($value): float
    {
        return $value / 1000;
    }
}
