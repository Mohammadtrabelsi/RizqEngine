<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\CustomerFactory;

/**
 * @property int $id
 * @property string $customer_name
 * @property string $customer_email
 */
class Customer extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = [];

    protected static function newFactory()
    {
        return CustomerFactory::new();
    }
}
