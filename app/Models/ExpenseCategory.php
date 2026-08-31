<?php

namespace App\Models;

use App\Traits\HasDefaultTranslations;
use App\Traits\RecordsActivity;
use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ExpenseCategory extends Model
{
    use HasDefaultTranslations, HasFactory, HasTranslations, RecordsActivity, TracksUserActions;

    protected $guarded = [];

    /** @var array<int, string> */
    public array $translatable = ['category_name', 'category_description'];

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id', 'id');
    }
}
