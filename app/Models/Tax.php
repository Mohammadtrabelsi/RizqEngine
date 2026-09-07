<?php

namespace App\Models;

use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A named tax rate (e.g. "VAT 19%") that can be applied, alone or combined
 * with others, to a purchase document.
 *
 * @property int $id
 * @property string $name
 * @property float $rate
 */
class Tax extends Model
{
    use HasFactory, TracksUserActions;

    protected $guarded = [];

    protected $casts = [
        'rate' => 'float',
    ];
}
