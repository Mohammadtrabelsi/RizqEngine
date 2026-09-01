<?php

namespace App\Models;

use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single product line of a Bon de Sortie.
 *
 * @property int $quantity
 * @property int $returned_quantity
 * @property int $lost_quantity
 */
class StockExitDetail extends Model
{
    use HasFactory, TracksUserActions;

    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'returned_quantity' => 'integer',
        'lost_quantity' => 'integer',
    ];

    /**
     * @return BelongsTo<StockExit, $this>
     */
    public function stockExit(): BelongsTo
    {
        return $this->belongsTo(StockExit::class, 'stock_exit_id', 'id');
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Quantity still expected back (not yet returned nor written off).
     */
    public function getOutstandingQuantityAttribute(): int
    {
        return $this->quantity - $this->returned_quantity - $this->lost_quantity;
    }
}
