<?php

namespace App\Models;

use App\Traits\TracksUserActions;
use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A sub-division (aisle/shelf/bin) within a warehouse.
 *
 * @property int $id
 * @property int $warehouse_id
 * @property string $name
 * @property string $code
 * @property bool $is_active
 * @property string|null $note
 */
class Location extends Model
{
    use HasFactory, TracksUserActions;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function newFactory()
    {
        return LocationFactory::new();
    }

    /**
     * @return BelongsTo<Warehouse, $this>
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
