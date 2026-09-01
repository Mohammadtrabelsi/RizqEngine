<?php

namespace App\Models;

use App\Enums\StockStatus;
use App\Traits\HasDefaultTranslations;
use App\Traits\RecordsActivity;
use App\Traits\TracksUserActions;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
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
 * @property Carbon|null $expiry_date
 * @property int $units_sold Transient attribute populated by reporting queries.
 * @property-read StockStatus $stock_status Derived from quantity and alert threshold.
 * @property-read bool $is_expired Whether the product's expiry date has passed.
 */
class Product extends Model implements HasMedia
{
    use HasDefaultTranslations, HasFactory, HasTranslations, InteractsWithMedia, RecordsActivity, TracksUserActions;

    protected $guarded = [];

    protected $with = ['media'];

    /** @var array<string, string> */
    protected $casts = [
        'expiry_date' => 'date',
    ];

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
     * Bon de Commande line items referencing this product.
     *
     * @return HasMany<BonCommandeDetails, $this>
     */
    public function bonCommandeDetails(): HasMany
    {
        return $this->hasMany(BonCommandeDetails::class, 'product_id', 'id');
    }

    /**
     * Commande line items referencing this product.
     *
     * @return HasMany<CommandeDetails, $this>
     */
    public function commandeDetails(): HasMany
    {
        return $this->hasMany(CommandeDetails::class, 'product_id', 'id');
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

    /**
     * Whether this product has an expiry date that is on or before today.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date !== null
            && $this->expiry_date->startOfDay()->lte(now()->startOfDay());
    }

    /**
     * Constrain a query to products whose expiry date has passed (or is today).
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->toDateString());
    }

    /**
     * Constrain a query to products expiring within the next $days days
     * (and not yet expired).
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>', now()->toDateString())
            ->whereDate('expiry_date', '<=', now()->addDays($days)->toDateString());
    }

    /**
     * Force this product out of stock by zeroing its on-hand quantity.
     * Returns true when a change was actually persisted.
     */
    public function markOutOfStock(): bool
    {
        if ((int) $this->product_quantity <= 0) {
            return false;
        }

        $this->product_quantity = 0;

        return $this->save();
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

    /**
     * Translation key describing this product's low-stock notification, based
     * on how far its quantity has fallen relative to the stock alert level.
     */
    public function stockAlertKey(): string
    {
        return match (true) {
            $this->product_quantity <= 0 => 'app.notif-desc-out',
            $this->product_quantity <= $this->product_stock_alert => 'app.notif-desc-critical',
            default => 'app.notif-desc-low',
        };
    }
}
