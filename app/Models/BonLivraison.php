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
 * Bon de Livraison (BL): the delivery note created by transforming a Commande.
 * It records the physical delivery of the ordered goods and may, in turn, be
 * invoiced (Facture / Sale) — the last, optional step of the shorter
 * Devis → Commande → Bon de Livraison → Facture path. It shares the monetary
 * structure of a Commande so the whole line/tax/discount/total set is carried
 * over unchanged.
 *
 * @property int $id
 * @property string $reference
 * @property int|null $commande_id
 * @property int $customer_id
 * @property string $status pending|delivered|invoiced
 */
class BonLivraison extends Model
{
    use GeneratesDocumentReference, HasFactory, RecordsActivity, TracksUserActions;

    public const STATUS_PENDING = 'pending';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_INVOICED = 'invoiced';

    protected $guarded = [];

    public function referencePrefix(): string
    {
        return 'BL';
    }

    /**
     * @return HasMany<BonLivraisonDetails, $this>
     */
    public function bonLivraisonDetails(): HasMany
    {
        return $this->hasMany(BonLivraisonDetails::class, 'bon_livraison_id', 'id');
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /**
     * The Commande this Bon de Livraison was created from.
     *
     * @return BelongsTo<Commande, $this>
     */
    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class, 'commande_id', 'id');
    }

    /**
     * The Facture (Sale) generated from this Bon de Livraison (if any).
     *
     * @return HasOne<Sale, $this>
     */
    public function sale(): HasOne
    {
        return $this->hasOne(Sale::class, 'bon_livraison_id', 'id');
    }

    public function isInvoiced(): bool
    {
        return $this->status === self::STATUS_INVOICED || $this->sale()->exists();
    }

    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED || $this->status === self::STATUS_INVOICED;
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
     * Bootstrap badge class representing this bon de livraison's status.
     */
    public function statusBadgeClass(): string
    {
        return [
            self::STATUS_PENDING => 'badge-info',
            self::STATUS_DELIVERED => 'badge-primary',
            self::STATUS_INVOICED => 'badge-success',
        ][$this->status] ?? 'badge-secondary';
    }
}
