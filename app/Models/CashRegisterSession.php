<?php

namespace App\Models;

use App\Enums\CashRegisterStatus;
use App\Traits\RecordsActivity;
use App\Traits\TracksUserActions;
use Database\Factories\CashRegisterSessionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A cashier's till session: opened with a cash float, closed with a counted
 * amount and an expected/counted difference (écart) for the daily Z report.
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $warehouse_id
 * @property int $opening_float
 * @property int|null $closing_amount
 * @property int|null $expected_amount
 * @property int|null $difference
 * @property CashRegisterStatus $status
 * @property Carbon $opened_at
 * @property Carbon|null $closed_at
 * @property string|null $note
 */
class CashRegisterSession extends Model
{
    use HasFactory, RecordsActivity, TracksUserActions;

    protected $guarded = [];

    protected $casts = [
        'opening_float' => 'integer',
        'closing_amount' => 'integer',
        'expected_amount' => 'integer',
        'difference' => 'integer',
        'status' => CashRegisterStatus::class,
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function newFactory()
    {
        return CashRegisterSessionFactory::new();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Warehouse, $this>
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', CashRegisterStatus::Open->value);
    }

    public function isOpen(): bool
    {
        return $this->status === CashRegisterStatus::Open;
    }
}
