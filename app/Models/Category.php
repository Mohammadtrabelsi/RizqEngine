<?php

namespace App\Models;

use App\Traits\HasDefaultTranslations;
use App\Traits\RecordsActivity;
use App\Traits\TracksUserActions;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property string $category_code
 * @property string $category_name
 * @property string|null $description
 * @property string|null $color
 * @property bool $is_active
 * @property-read string $image_url URL of the category image, or the configured fallback.
 */
class Category extends Model implements HasMedia
{
    use HasDefaultTranslations, HasFactory, HasTranslations, InteractsWithMedia, RecordsActivity, TracksUserActions;

    protected $guarded = [];

    /** @var array<string, string> */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** @var array<int, string> */
    public array $translatable = ['category_name', 'description'];

    protected static function newFactory()
    {
        return CategoryFactory::new();
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->singleFile()
            ->useFallbackUrl(default_category_image());
    }

    /**
     * URL of the category image, or the configured fallback when none is set.
     */
    public function getImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('images');
    }
}
