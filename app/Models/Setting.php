<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Currency;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['currency'];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'default_currency_id', 'id');
    }
}
