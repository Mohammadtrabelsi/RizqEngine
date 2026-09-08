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
 * @property int $supplier_id
 * @property string $supplier_name
 * @property int|null $warehouse_id
 * @property string $status
 * @property string $payment_status
 */
class Purchase extends Model
{
    use HasFactory, RecordsActivity, TracksUserActions;

    protected $guarded = [];

    /**
     * @return HasMany<PurchaseDetail, $this>
     */
    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id', 'id');
    }

    /**
     * The warehouse (dépôt) this purchase stocks its products into.
     *
     * @return BelongsTo<Warehouse, $this>
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    /**
     * @return HasMany<PurchasePayment, $this>
     */
    public function purchasePayments(): HasMany
    {
        return $this->hasMany(PurchasePayment::class, 'purchase_id', 'id');
    }

    /**
     * The withholding-tax (retenue à la source) snapshots applied to this
     * purchase.
     *
     * @return HasMany<PurchaseWithholdingTax, $this>
     */
    public function withholdingTaxes(): HasMany
    {
        return $this->hasMany(PurchaseWithholdingTax::class, 'purchase_id', 'id');
    }

    /**
     * The supplier this purchase was invoiced by.
     *
     * @return BelongsTo<Supplier, $this>
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    /**
     * The net amount actually payable to the supplier: TTC minus the total
     * withholding retained.
     */
    public function getNetPayableAttribute(): float
    {
        return round($this->total_amount - $this->withholding_amount, 3);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $number = Purchase::max('id') + 1;
            $model->reference = make_reference_id('PR', $number);
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

    /**
     * Total withholding (retenue à la source) retained. Persisted in millimes
     * (× 1000) to keep the Tunisian dinar's three-decimal precision.
     */
    public function getWithholdingAmountAttribute($value)
    {
        return ((int) $value) / 1000;
    }

    public function getDiscountAmountAttribute($value)
    {
        return $value / 100;
    }
}
