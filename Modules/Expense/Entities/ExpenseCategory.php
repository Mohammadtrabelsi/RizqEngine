<?php

namespace Modules\Expense\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\RecordsActivity;

class ExpenseCategory extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = [];

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id', 'id');
    }
}
