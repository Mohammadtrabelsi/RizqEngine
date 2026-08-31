<?php

namespace App\Models;

use App\Traits\HasDefaultTranslations;
use App\Traits\TracksUserActions;
use Database\Factories\UnitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Unit extends Model
{
    use HasDefaultTranslations, HasFactory, HasTranslations, TracksUserActions;

    protected $guarded = [];

    /** @var array<int, string> */
    public array $translatable = ['name', 'short_name'];

    protected static function newFactory()
    {
        return UnitFactory::new();
    }
}
