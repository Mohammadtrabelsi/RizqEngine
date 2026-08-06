<?php

namespace Modules\People\Entities;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\People\Database\factories\SupplierFactory;

/**
 * @property int $id
 * @property string $supplier_name
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
