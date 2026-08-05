<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Setting\Database\factories\UnitFactory;

class Unit extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return UnitFactory::new();
    }
}
