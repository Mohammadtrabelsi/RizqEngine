<?php

namespace Modules\People\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\RecordsActivity;

class Supplier extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = [];

    protected static function newFactory() {
        return \Modules\People\Database\factories\SupplierFactory::new();
    }
}
