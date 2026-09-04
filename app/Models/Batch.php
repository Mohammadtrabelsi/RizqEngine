<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use App\Traits\TracksUserActions;
use Database\Factories\BatchFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A production lot of a product, carrying its own quantity and expiry
 * (DLC/DLUO) for traceability.
 *
 * @property int $id
 * @property int $product_id
 * @property int|null $warehouse_id
 * @property string $batch_number
 * @property int $quantity
 * @property Carbon|null $manufactured_date
 * @property Carbon|null $expiry_date
 * @property string|null $note
 * @property-read bool $is_expired
 */
class Batch extends Model
{
    use HasFactory, RecordsActivity, TracksUserActions;

    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'manufactured_date' => 'date',
        'expiry_date' => 'date',
    ];

    protected static function newFactory()
    {
        return BatchFactory::new();
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<Warehouse, $this>
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * @return HasMany<SerialNumber, $this>
     */
    public function serialNumbers(): HasMany
    {
        return $this->hasMany(SerialNumber::class);
    }

    /**
     * Whether the batch's expiry date has passed.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date !== null && $this->expiry_date->isPast();
    }

    /**
     * Batches expiring within the given number of days (and not yet expired).
     */
    public function scopeExpiringWithin(Builder $query, int $days): Builder
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', now()->toDateString())
            ->whereDate('expiry_date', '<=', now()->addDays($days)->toDateString());
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now()->toDateString());
    }
}
