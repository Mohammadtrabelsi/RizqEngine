<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Bon de Sortie (BS): material that has left the stock and is expected back.
 *
 * @property int $id
 * @property string $reference
 * @property string $status in_transit|closed
 */
class StockExit extends Model
{
    use HasFactory, RecordsActivity, TracksUserActions;

    public const STATUS_IN_TRANSIT = 'in_transit';

    public const STATUS_CLOSED = 'closed';

    protected $guarded = [];

    /**
     * @return HasMany<StockExitDetail, $this>
     */
    public function details(): HasMany
    {
        return $this->hasMany(StockExitDetail::class, 'stock_exit_id', 'id');
    }

    /**
     * @return HasMany<StockEntry, $this>
     */
    public function entries(): HasMany
    {
        return $this->hasMany(StockEntry::class, 'stock_exit_id', 'id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function (StockExit $model) {
            if (empty($model->reference)) {
                $number = (int) StockExit::max('id') + 1;
                $model->reference = 'BS-'.now()->format('Y').'-'.str_pad((string) $number, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
