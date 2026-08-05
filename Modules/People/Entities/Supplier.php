<?php

namespace Modules\People\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\People\Database\factories\SupplierFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return SupplierFactory::new();
    }
}
