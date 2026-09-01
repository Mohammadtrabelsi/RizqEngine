<?php

namespace App\Models;

use App\Traits\GeneratesDocumentReference;
use App\Traits\RecordsActivity;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Bon de Commande (BC): the customer order created by transforming an accepted
 * Devis (Quotation). Shares the exact monetary structure of a Quotation so the
 * whole line/tax/discount/total set is carried over unchanged.
 *
 * @property int $id
 * @property string $reference
 * @property int|null $quotation_id
 * @property int $customer_id
 * @property string $status draft|confirmed|converted|cancelled
 */
class BonCommande extends Model
{
    use GeneratesDocumentReference, HasFactory, RecordsActivity, TracksUserActions;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_CONVERTED = 'converted';

    public const STATUS_CANCELLED = 'cancelled';

    protected $guarded = [];

    public function referencePrefix(): string
    {
        return 'BC';
    }

    /**
     * @return HasMany<BonCommandeDetails, $this>
     */
    public function bonCommandeDetails(): HasMany
    {
        return $this->hasMany(BonCommandeDetails::class, 'bon_commande_id', 'id');
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /**
     * The Devis this Bon de Commande was created from.
     *
     * @return BelongsTo<Quotation, $this>
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'id');
    }

    /**
     * The Commande this Bon de Commande was converted into (if any).
     *
     * @return HasOne<Commande, $this>
     */
    public function commande(): HasOne
    {
        return $this->hasOne(Commande::class, 'bon_commande_id', 'id');
    }

    public function isConverted(): bool
    {
        return $this->status === self::STATUS_CONVERTED || $this->commande()->exists();
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('d M, Y');
    }

    public function getShippingAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getTotalAmountAttribute($value)
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

    /**
     * Bootstrap badge class representing this bon de commande's status.
     */
    public function statusBadgeClass(): string
    {
        return [
            self::STATUS_DRAFT => 'badge-info',
            self::STATUS_CONFIRMED => 'badge-primary',
            self::STATUS_CONVERTED => 'badge-success',
            self::STATUS_CANCELLED => 'badge-danger',
        ][$this->status] ?? 'badge-secondary';
    }
}
