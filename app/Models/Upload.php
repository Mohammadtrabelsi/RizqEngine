<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property string $folder
 * @property string $filename
 */
class Upload extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];
}
