<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use App\Traits\TracksUserActions;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property string $customer_name
 * @property string $client_type
 * @property string $customer_email
 * @property string|null $whatsapp_number
 * @property string|null $responsible_person
 * @property string|null $tax_identification_number
 * @property string|null $iban
 * @property string|null $note
 */
class Customer extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, RecordsActivity, TracksUserActions;

    /**
     * Client classified as an individual (a physical person).
     */
    public const TYPE_PHYSICAL_PERSON = 'physical_person';

    /**
     * Client classified as an organisation (a legal entity / company).
     * Legal entities are required to provide a tax identification number
     * (matricule fiscal).
     */
    public const TYPE_LEGAL_ENTITY = 'legal_entity';

    protected $guarded = [];

    /**
     * The supported client classifications.
     *
     * @return array<int, string>
     */
    public static function clientTypes(): array
    {
        return [
            self::TYPE_PHYSICAL_PERSON,
            self::TYPE_LEGAL_ENTITY,
        ];
    }

    /**
     * Whether the client is classified as a legal entity, and therefore
     * required to carry a tax identification number (matricule fiscal).
     */
    public function isLegalEntity(): bool
    {
        return $this->client_type === self::TYPE_LEGAL_ENTITY;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useFallbackUrl(default_customer_image());
    }

    /**
     * URL of the customer's profile image, or the configured fallback when
     * none is set.
     */
    public function getImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('images');
    }

    /**
     * URL of the customer's uploaded description document, or an empty string
     * when none is set.
     */
    public function getDocumentUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('documents');
    }

    protected static function newFactory()
    {
        return CustomerFactory::new();
    }

    /** @var array<string, string> */
    protected $casts = [
        'credit_limit' => 'integer',
        'current_balance' => 'integer',
    ];

    /**
     * Remaining credit head-room, in cents (never negative).
     */
    public function availableCredit(): int
    {
        return max(0, (int) $this->credit_limit - (int) $this->current_balance);
    }

    /**
     * Whether this customer is currently over their approved credit limit.
     */
    public function isOverCreditLimit(): bool
    {
        return (int) $this->credit_limit > 0 && (int) $this->current_balance > (int) $this->credit_limit;
    }

    /**
     * Whether credit sales are permitted for this customer.
     */
    public function allowsCredit(): bool
    {
        return (int) $this->credit_limit > 0;
    }
}
