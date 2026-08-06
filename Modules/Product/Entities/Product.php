<?php

namespace Modules\Product\Entities;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Product\Database\factories\ProductFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property int $category_id
 * @property string $product_name
 * @property string $product_code
 * @property string $product_barcode_symbology
 * @property int $product_quantity
 * @property float $product_cost
 * @property float $product_price
 * @property string $product_unit
 * @property int $product_stock_alert
 * @property int $product_order_tax
 * @property int $product_tax_type
 * @property string|null $product_note
 * @property int $units_sold Transient attribute populated by reporting queries.
 */
class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, RecordsActivity;

    protected $guarded = [];

    protected $with = ['media'];

    protected static function newFactory()
    {
        return ProductFactory::new();
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * @return HasMany<StockMovement, $this>
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'product_id', 'id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useFallbackUrl('/images/fallback_product_image.png');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(50)
            ->height(50);
    }

    public function setProductCostAttribute($value)
    {
        $this->attributes['product_cost'] = ($value * 100);
    }

    public function getProductCostAttribute($value)
    {
        return $value / 100;
    }

    public function setProductPriceAttribute($value)
    {
        $this->attributes['product_price'] = ($value * 100);
    }

    public function getProductPriceAttribute($value)
    {
        return $value / 100;
    }
}
