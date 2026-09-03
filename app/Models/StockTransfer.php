<?php

namespace App\Models;

use App\Enums\TransferStatus;
use App\Traits\RecordsActivity;
use App\Traits\TracksUserActions;
use Database\Factories\StockTransferFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A movement of stock from one warehouse to another.
 *
 * @property int $id
 * @property string $reference
 * @property int $from_warehouse_id
 * @property int $to_warehouse_id
 * @property \Illuminate\Support\Carbon $date
 * @property TransferStatus $status
 * @property string|null $note
 */
class StockTransfer extends Model
{
    use HasFactory, RecordsActivity, TracksUserActions;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'status' => TransferStatus::class,
    ];

    protected static function newFactory()
    {
        return StockTransferFactory::new();
    }

    /**
     * @return BelongsTo<Warehouse, $this>
     */
    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    /**
     * @return BelongsTo<Warehouse, $this>
     */
    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    /**
     * @return HasMany<StockTransferLine, $this>
     */
    public function lines(): HasMany
    {
        return $this->hasMany(StockTransferLine::class);
    }
}
