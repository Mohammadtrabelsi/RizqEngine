<?php

namespace Modules\Upload\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Upload extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];
}
