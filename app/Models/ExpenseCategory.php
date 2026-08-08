<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = [];

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id', 'id');
    }
}
