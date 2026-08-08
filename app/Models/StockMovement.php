<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single, immutable entry in the stock ledger.
 *
 * @property int $product_id
 * @property int $quantity Magnitude of the change (always positive)
 * @property int $quantity_before
 * @property int $quantity_after
 * @property string $type in|out|opening|adjustment
 */
class StockMovement extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
    ];

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeIn(Builder $query): Builder
    {
        return $query->where('type', 'in');
    }

    public function scopeOut(Builder $query): Builder
    {
        return $query->where('type', 'out');
    }

    /**
     * Signed change this movement represents (positive = stock came in).
     */
    public function getSignedQuantityAttribute(): int
    {
        return $this->type === 'out' ? -$this->quantity : $this->quantity;
    }
}
