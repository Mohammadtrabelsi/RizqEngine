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
    use GeneratesDocumentReference, HasFactory, RecordsActivity, TracksUserActions;

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_INVOICED = 'invoiced';

    public const SHIPPING_NOT_SHIPPED = 'not_shipped';

    public const SHIPPING_PARTIALLY_SHIPPED = 'partially_shipped';

    public const SHIPPING_SHIPPED = 'shipped';

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
     * The Devis this Commande was created directly from (shorter path), if any.
     *
     * @return BelongsTo<Quotation, $this>
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'id');
    }

    /**
     * The first/latest Bon de Livraison generated from this Commande (if any).
     * Kept for the document-chain display; use {@see bonLivraisons()} to reach
     * every (partial) delivery note.
     *
     * @return HasOne<BonLivraison, $this>
     */
    public function bonLivraison(): HasOne
    {
        return $this->hasOne(BonLivraison::class, 'commande_id', 'id');
    }

    /**
     * All Bons de Livraison generated from this Commande. With partial shipping
     * a single order may be delivered across several delivery notes.
     *
     * @return HasMany<BonLivraison, $this>
     */
    public function bonLivraisons(): HasMany
    {
        return $this->hasMany(BonLivraison::class, 'commande_id', 'id');
    }

    public function hasBonLivraison(): bool
    {
        return $this->bonLivraison()->exists();
    }

    /**
     * Quantity already delivered per Commande line, keyed by commande_detail id.
     * Sums the quantities of every Bon de Livraison line that traces back to the
     * ordered line.
     *
     * @return array<int, int>
     */
    public function deliveredQuantities(): array
    {
        return BonLivraisonDetails::query()
            ->whereIn('bon_livraison_id', $this->bonLivraisons()->select('id'))
            ->whereNotNull('commande_detail_id')
            ->groupBy('commande_detail_id')
            ->selectRaw('commande_detail_id, SUM(quantity) as delivered')
            ->pluck('delivered', 'commande_detail_id')
            ->map(fn ($value) => (int) $value)
            ->all();
    }

    /**
     * Quantity still to deliver per Commande line, keyed by commande_detail id.
     * Never negative.
     *
     * @return array<int, int>
     */
    public function remainingQuantities(): array
    {
        $delivered = $this->deliveredQuantities();

        return $this->commandeDetails
            ->mapWithKeys(function (CommandeDetails $detail) use ($delivered) {
                $remaining = (int) $detail->getRawOriginal('quantity') - ($delivered[$detail->id] ?? 0);

                return [$detail->id => max(0, $remaining)];
            })
            ->all();
    }

    /**
     * Whether every ordered quantity has been covered by delivery notes.
     */
    public function isFullyDelivered(): bool
    {
        return $this->commandeDetails->isNotEmpty()
            && collect($this->remainingQuantities())->every(fn (int $remaining) => $remaining === 0);
    }

    /**
     * Whether some — but not all — of the ordered quantities have been delivered.
     */
    public function isPartiallyDelivered(): bool
    {
        return $this->bonLivraisons()->exists() && ! $this->isFullyDelivered();
    }

    /**
     * Derive the shipping status from the current delivery progress. A Commande
     * is only ever 'shipped' once every ordered quantity has been delivered.
     */
    public function resolveShippingStatus(): string
    {
        if ($this->isFullyDelivered()) {
            return self::SHIPPING_SHIPPED;
        }

        if ($this->bonLivraisons()->exists()) {
            return self::SHIPPING_PARTIALLY_SHIPPED;
        }

        return self::SHIPPING_NOT_SHIPPED;
    }

    /**
     * Recompute and persist the shipping status after a delivery note changes.
     */
    public function syncShippingStatus(): void
    {
        $this->load('commandeDetails');

        $this->forceFill(['shipping_status' => $this->resolveShippingStatus()])->save();
    }

    public function shippingStatusBadgeClass(): string
    {
        return [
            self::SHIPPING_NOT_SHIPPED => 'badge-secondary',
            self::SHIPPING_PARTIALLY_SHIPPED => 'badge-warning',
            self::SHIPPING_SHIPPED => 'badge-success',
        ][$this->shipping_status] ?? 'badge-secondary';
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

    /**
     * The Bons de Sortie (consignment exits) generated from this Commande.
     *
     * @return HasMany<StockExit, $this>
     */
    public function stockExits(): HasMany
    {
        return $this->hasMany(StockExit::class, 'commande_id', 'id');
    }

    public function hasStockExit(): bool
    {
        return $this->stockExits()->exists();
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

    /**
     * Bootstrap badge class representing this commande's status.
     */
    public function statusBadgeClass(): string
    {
        return [
            self::STATUS_PENDING => 'badge-info',
            self::STATUS_CONFIRMED => 'badge-primary',
            self::STATUS_INVOICED => 'badge-success',
        ][$this->status] ?? 'badge-secondary';
    }
}
