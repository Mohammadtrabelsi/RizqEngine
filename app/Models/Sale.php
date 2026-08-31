<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $reference
 * @property int $customer_id
 * @property string $customer_name
 * @property string $status
 * @property string $payment_status
 */
class Sale extends Model
{
    use HasFactory, RecordsActivity, TracksUserActions;

    protected $guarded = [];

    /**
     * @return HasMany<SaleDetails, $this>
     */
    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetails::class, 'sale_id', 'id');
    }

    /**
     * @return HasMany<SalePayment, $this>
     */
    public function salePayments(): HasMany
    {
        return $this->hasMany(SalePayment::class, 'sale_id', 'id');
    }

    /**
     * The Commande this Facture (Sale) was generated from (if any).
     *
     * @return BelongsTo<Commande, $this>
     */
    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class, 'commande_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $number = Sale::max('id') + 1;
            $model->reference = make_reference_id('SL', $number);
        });
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function getShippingAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getPaidAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getTotalAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getDueAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getTaxAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getDiscountAmountAttribute($value)
    {
        return $value / 100;
    }
}
