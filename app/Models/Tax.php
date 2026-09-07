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
}
