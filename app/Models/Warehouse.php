<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use App\Traits\TracksUserActions;
use Database\Factories\WarehouseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A physical storage site (dépôt) holding stock.
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $phone
 * @property string|null $city
 * @property string|null $address
 * @property bool $is_default
 * @property bool $is_active
 * @property string|null $note
 */
class Warehouse extends Model
{
    use HasFactory, RecordsActivity, TracksUserActions;

    protected $guarded = [];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function newFactory()
    {
        return WarehouseFactory::new();
    }

    /**
     * @return HasMany<Location, $this>
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    /**
     * @return BelongsToMany<Product, $this>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_warehouse')
            ->withPivot(['quantity', 'location_id'])
            ->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * The default warehouse used when no explicit warehouse is chosen.
     */
    public static function default(): ?self
    {
        return static::query()->where('is_default', true)->first();
    }
}
