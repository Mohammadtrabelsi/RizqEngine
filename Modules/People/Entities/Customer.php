<?php

namespace Modules\People\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\People\Database\factories\CustomerFactory;

class Customer extends Model
{
    use HasFactory;
use App\Traits\RecordsActivity;

class Customer extends Model
{

    use HasFactory, RecordsActivity;

    protected $guarded = [];

    protected static function newFactory()
    {
        return CustomerFactory::new();
    }
}
