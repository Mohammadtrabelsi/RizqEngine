<?php

namespace Modules\Currency\Entities;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = [];
}
