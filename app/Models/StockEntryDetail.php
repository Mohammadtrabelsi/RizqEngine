<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single product line of a Bon d'Entrée.
 *
 * @property int $quantity_out
 * @property int $quantity_returned
 * @property int $quantity_lost
 */
class StockEntryDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'quantity_out' => 'integer',
        'quantity_returned' => 'integer',
        'quantity_lost' => 'integer',
    ];

    /**
     * @return BelongsTo<StockEntry, $this>
     */
    public function stockEntry(): BelongsTo
    {
        return $this->belongsTo(StockEntry::class, 'stock_entry_id', 'id');
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
