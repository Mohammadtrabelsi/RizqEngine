<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $reference
 * @property int $customer_id
 */
class Quotation extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = [];

    /**
     * @return HasMany<QuotationDetails, $this>
     */
    public function quotationDetails(): HasMany
    {
        return $this->hasMany(QuotationDetails::class, 'quotation_id', 'id');
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /**
     * The Bon de Commande this Devis was converted into (if any).
     *
     * @return HasOne<BonCommande, $this>
     */
    public function bonCommande(): HasOne
    {
        return $this->hasOne(BonCommande::class, 'quotation_id', 'id');
    }

    public function isConverted(): bool
    {
        return $this->bonCommande()->exists();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $number = Quotation::max('id') + 1;
            $model->reference = make_reference_id('QT', $number);
        });
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('d M, Y');
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
