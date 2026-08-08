<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = [];

    protected static function newFactory()
    {
        return CategoryFactory::new();
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
