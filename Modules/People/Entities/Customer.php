<?php

namespace Modules\People\Entities;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\People\Database\factories\CustomerFactory;

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
