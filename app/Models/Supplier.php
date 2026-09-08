<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use App\Traits\TracksUserActions;
use Database\Factories\SupplierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property string $supplier_name
 * @property string|null $whatsapp_number
 * @property string|null $responsible_person
 * @property string|null $tax_identification_number
 * @property string|null $iban
 * @property string|null $note
 * @property bool $subject_to_withholding
 * @property string|null $legal_form
 * @property string|null $fiscal_category
 */
class Supplier extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, RecordsActivity, TracksUserActions;

    protected $casts = [
        'subject_to_withholding' => 'boolean',
    ];

    protected $guarded = [];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useFallbackUrl(default_supplier_image());
    }

    /**
     * URL of the supplier's profile image, or the configured fallback when
     * none is set.
     */
    public function getImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('images');
    }

    /**
     * URL of the supplier's uploaded description document, or an empty string
     * when none is set.
     */
    public function getDocumentUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('documents');
    }

    protected static function newFactory()
    {
        return SupplierFactory::new();
    }

    /**
     * @return HasMany<Product, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'supplier_id', 'id');
    }
}
