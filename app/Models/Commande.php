<?php

namespace App\Models;

use App\Traits\GeneratesDocumentReference;
use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Commande: the confirmed customer order created by transforming a Bon de
 * Commande. It is the last step before invoicing (Facture / Sale).
 *
 * @property int $id
 * @property string $reference
 * @property int|null $bon_commande_id
 * @property int $customer_id
 * @property string $status pending|confirmed|invoiced
 */
class Commande extends Model
{
    use GeneratesDocumentReference, HasFactory, RecordsActivity;

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_INVOICED = 'invoiced';

    protected $guarded = [];

    public function referencePrefix(): string
    {
        return 'CMD';
    }

    /**
     * @return HasMany<CommandeDetails, $this>
     */
    public function commandeDetails(): HasMany
    {
        return $this->hasMany(CommandeDetails::class, 'commande_id', 'id');
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /**
     * The Bon de Commande this Commande was created from.
     *
     * @return BelongsTo<BonCommande, $this>
     */
    public function bonCommande(): BelongsTo
    {
        return $this->belongsTo(BonCommande::class, 'bon_commande_id', 'id');
    }

    /**
     * The Facture (Sale) generated from this Commande (if any).
     *
     * @return HasOne<Sale, $this>
     */
    public function sale(): HasOne
    {
        return $this->hasOne(Sale::class, 'commande_id', 'id');
    }

    public function isInvoiced(): bool
    {
        return $this->status === self::STATUS_INVOICED || $this->sale()->exists();
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
}
