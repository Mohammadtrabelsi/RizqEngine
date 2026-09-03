<?php

namespace App\Models;

use App\Enums\SerialStatus;
use App\Traits\RecordsActivity;
use App\Traits\TracksUserActions;
use Database\Factories\SerialNumberFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An individually tracked (serialised) unit of a product.
 *
 * @property int $id
 * @property int $product_id
 * @property int|null $batch_id
 * @property int|null $warehouse_id
 * @property string $serial
 * @property SerialStatus $status
 * @property string|null $note
 */
class SerialNumber extends Model
{
    use HasFactory, RecordsActivity, TracksUserActions;

    protected $guarded = [];

    protected $casts = [
        'status' => SerialStatus::class,
    ];

    protected static function newFactory()
    {
        return SerialNumberFactory::new();
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<Batch, $this>
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * @return BelongsTo<Warehouse, $this>
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function scopeStatus(Builder $query, SerialStatus $status): Builder
    {
        return $query->where('status', $status->value);
    }
}
