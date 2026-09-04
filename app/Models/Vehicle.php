<?php

namespace App\Models;

use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A vehicle (véhicule) that can be assigned to a Bon de Sortie.
 *
 * @property int $id
 * @property string $registration
 * @property string|null $brand
 * @property string|null $model
 * @property string|null $note
 */
class Vehicle extends Model
{
    use HasFactory, TracksUserActions;

    protected $guarded = [];

    /**
     * @return HasMany<StockExit, $this>
     */
    public function stockExits(): HasMany
    {
        return $this->hasMany(StockExit::class, 'vehicle_id', 'id');
    }

    /**
     * A readable label combining brand/model and registration.
     */
    public function getLabelAttribute(): string
    {
        $name = trim(($this->brand ?? '').' '.($this->model ?? ''));

        return $name !== '' ? $name.' — '.$this->registration : $this->registration;
    }
}
