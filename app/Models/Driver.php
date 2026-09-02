<?php

namespace App\Models;

use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A driver (chauffeur) that can be assigned to a Bon de Sortie.
 *
 * @property int $id
 * @property string $name
 * @property string|null $phone
 * @property string|null $license_number
 * @property string|null $note
 */
class Driver extends Model
{
    use HasFactory, TracksUserActions;

    protected $guarded = [];

    /**
     * @return HasMany<StockExit, $this>
     */
    public function stockExits(): HasMany
    {
        return $this->hasMany(StockExit::class, 'driver_id', 'id');
    }
}
