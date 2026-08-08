<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;

/**
 * @property int $id
 * @property int $adjustment_id
 * @property int $product_id
 * @property int $quantity
 * @property string $type
 */
class AdjustedProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['product'];

    /**
     * @return BelongsTo<Adjustment, $this>
     */
    public function adjustment(): BelongsTo
    {
        return $this->belongsTo(Adjustment::class, 'adjustment_id', 'id');
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
