<?php

namespace Modules\Adjustment\Entities;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Adjustment extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = [];

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('d M, Y');
    }

    /**
     * @return HasMany<AdjustedProduct, $this>
     */
    public function adjustedProducts(): HasMany
    {
        return $this->hasMany(AdjustedProduct::class, 'adjustment_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $number = Adjustment::max('id') + 1;
            $model->reference = make_reference_id('ADJ', $number);
        });
    }
}
