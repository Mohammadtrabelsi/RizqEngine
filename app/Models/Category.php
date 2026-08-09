<?php

namespace App\Models;

use App\Traits\HasDefaultTranslations;
use App\Traits\RecordsActivity;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasDefaultTranslations, HasFactory, HasTranslations, RecordsActivity;

    protected $guarded = [];

    /** @var array<int, string> */
    public array $translatable = ['category_name'];

    protected static function newFactory()
    {
        return CategoryFactory::new();
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
