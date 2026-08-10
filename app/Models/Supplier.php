<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use Database\Factories\SupplierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $supplier_name
 * @property string|null $tax_identification_number
 */
class Supplier extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = [];

    protected static function newFactory()
    {
        return SupplierFactory::new();
    }
}
