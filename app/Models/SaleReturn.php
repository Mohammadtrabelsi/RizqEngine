<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $reference
 * @property int $customer_id
 * @property string $customer_name
 * @property string $status
 * @property string $payment_status
 */
class SaleReturn extends Model
{
    use HasFactory, RecordsActivity, TracksUserActions;

    protected $guarded = [];

    /**
     * @return HasMany<SaleReturnDetail, $this>
     */
    public function saleReturnDetails(): HasMany
    {
        return $this->hasMany(SaleReturnDetail::class, 'sale_return_id', 'id');
    }

    /**
     * @return HasMany<SaleReturnPayment, $this>
     */
    public function saleReturnPayments(): HasMany
    {
        return $this->hasMany(SaleReturnPayment::class, 'sale_return_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $number = SaleReturn::max('id') + 1;
            $model->reference = make_reference_id('SLRN', $number);
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
