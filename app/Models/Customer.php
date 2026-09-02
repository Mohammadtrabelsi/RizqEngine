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

    protected $guarded = [];

    /**
     * URL of the customer's profile image, or an empty string when none is set.
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
}
