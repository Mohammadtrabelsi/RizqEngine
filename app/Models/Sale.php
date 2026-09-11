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

    /**
     * The Bon de Livraison this Facture (Sale) was generated from (if any).
     *
     * @return BelongsTo<BonLivraison, $this>
     */
    public function bonLivraison(): BelongsTo
    {
        return $this->belongsTo(BonLivraison::class, 'bon_livraison_id', 'id');
    }

    /**
     * The withholding taxes (retenues à la source) applied to this sale, each
     * an immutable snapshot taken at invoice time.
     *
     * @return HasMany<SaleWithholdingTax, $this>
     */
    public function withholdingTaxes(): HasMany
    {
        return $this->hasMany(SaleWithholdingTax::class, 'sale_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Legal, uninterrupted numbering: the reference is allocated from a
            // dedicated monotonic counter (never max(id)+1, which races and
            // reuses numbers after a deletion). It is assigned here so it can
            // never be set or overridden from user input.
            $model->reference = app(\App\Services\DocumentNumberService::class)->next('sale', 'SL');
        });
    }

    /**
     * Total withholding (retenue à la source) deducted from the TTC. Stored in
     * millimes (× 1000) to preserve the Tunisian dinar's three decimals.
     */
    public function getWithholdingAmountAttribute($value): float
    {
        return ($value ?? 0) / 1000;
    }

    /**
     * The net actually receivable from the customer: TTC minus the withholding
     * the customer is entitled to retain. With no RAS it equals the TTC.
     */
    public function getNetPayableAttribute(): float
    {
        return round($this->total_amount - $this->withholding_amount, 3);
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
