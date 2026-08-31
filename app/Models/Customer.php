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
 * @property string|null $tax_identification_number
 */
class Customer extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, RecordsActivity, TracksUserActions;

    protected $guarded = [];

    protected static function newFactory()
    {
        return CustomerFactory::new();
    }
}
