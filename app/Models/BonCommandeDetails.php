<?php

namespace App\Models;

use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $bon_commande_id
 * @property int $product_id
 * @property string $product_name
 * @property string $product_code
 * @property int $quantity
 * @property string $product_discount_type
 */
class BonCommandeDetails extends Model
{
    use HasFactory, TracksUserActions;

    protected $guarded = [];

    protected $with = ['product'];

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * @return BelongsTo<BonCommande, $this>
     */
    public function bonCommande(): BelongsTo
    {
        return $this->belongsTo(BonCommande::class, 'bon_commande_id', 'id');
    }

    public function getPriceAttribute($value)
    {
        return $value / 100;
    }

    public function getUnitPriceAttribute($value)
    {
        return $value / 100;
    }

    public function getSubTotalAttribute($value)
    {
        return $value / 100;
    }

    public function getProductDiscountAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getProductTaxAmountAttribute($value)
    {
        return $value / 100;
    }
}
