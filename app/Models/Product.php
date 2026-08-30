<?php

namespace App\Models;

use App\Enums\StockStatus;
use App\Traits\HasDefaultTranslations;
use App\Traits\RecordsActivity;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property int $category_id
 * @property int|null $supplier_id
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
 * @property-read StockStatus $stock_status Derived from quantity and alert threshold.
 */
class Product extends Model implements HasMedia
{
    use HasDefaultTranslations, HasFactory, HasTranslations, InteractsWithMedia, RecordsActivity;

    protected $guarded = [];

    protected $with = ['media'];

    /** @var array<int, string> */
    public array $translatable = ['product_name', 'product_note'];

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
     * @return BelongsTo<Supplier, $this>
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    /**
     * @return HasMany<StockMovement, $this>
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'product_id', 'id');
    }

    /**
     * Sale line items referencing this product.
     *
     * @return HasMany<SaleDetails, $this>
     */
    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetails::class, 'product_id', 'id');
    }

    /**
     * Purchase line items referencing this product.
     *
     * @return HasMany<PurchaseDetail, $this>
     */
    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class, 'product_id', 'id');
    }

    /**
     * Sale-return line items referencing this product.
     *
     * @return HasMany<SaleReturnDetail, $this>
     */
    public function saleReturnDetails(): HasMany
    {
        return $this->hasMany(SaleReturnDetail::class, 'product_id', 'id');
    }

    /**
     * Purchase-return line items referencing this product.
     *
     * @return HasMany<PurchaseReturnDetail, $this>
     */
    public function purchaseReturnDetails(): HasMany
    {
        return $this->hasMany(PurchaseReturnDetail::class, 'product_id', 'id');
    }

    /**
     * Derived stock status for this product.
     */
    public function getStockStatusAttribute(): StockStatus
    {
        return StockStatus::fromQuantity(
            (int) $this->product_quantity,
            (int) $this->product_stock_alert
        );
    }

    /**
     * Constrain a query to products matching the given stock status.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeStockStatus(Builder $query, StockStatus $status): Builder
    {
        return match ($status) {
            StockStatus::OutOfStock => $query->where('product_quantity', '<=', 0),
            StockStatus::LowStock => $query->where('product_quantity', '>', 0)
                ->whereColumn('product_quantity', '<=', 'product_stock_alert'),
            StockStatus::InStock => $query->whereColumn('product_quantity', '>', 'product_stock_alert'),
        };
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useFallbackUrl(default_product_image());
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
