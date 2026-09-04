<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Bon d'Entrée (BE): closes a Bon de Sortie by recording what came back.
 *
 * @property int $id
 * @property string $reference
 * @property int $stock_exit_id
 */
class StockEntry extends Model
{
    use HasFactory, RecordsActivity, TracksUserActions;

    protected $guarded = [];

    /**
     * @return HasMany<StockEntryDetail, $this>
     */
    public function details(): HasMany
    {
        return $this->hasMany(StockEntryDetail::class, 'stock_entry_id', 'id');
    }

    /**
     * @return BelongsTo<StockExit, $this>
     */
    public function stockExit(): BelongsTo
    {
        return $this->belongsTo(StockExit::class, 'stock_exit_id', 'id');
    }

    /**
     * The invoice generated when a consignment exit is regularised (nullable
     * for standard returns and for consignment returns with nothing sold).
     *
     * @return BelongsTo<Sale, $this>
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function (StockEntry $model) {
            if (empty($model->reference)) {
                $number = (int) StockEntry::max('id') + 1;
                $model->reference = 'BE-'.now()->format('Y').'-'.str_pad((string) $number, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
