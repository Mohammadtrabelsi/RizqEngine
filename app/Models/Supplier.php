<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use Database\Factories\SupplierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property string $supplier_name
 */
class Supplier extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, RecordsActivity;

    protected $guarded = [];

    protected static function newFactory()
    {
        return SupplierFactory::new();
    }
}
