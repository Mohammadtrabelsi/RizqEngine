<?php

namespace App\Models;

use App\Services\DocumentNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A monotonic numbering counter for a family of fiscal documents (e.g. sales
 * invoices). Allocation is performed exclusively through
 * {@see DocumentNumberService} under a row lock so numbers are
 * gapless per allocation and never reused.
 *
 * @property int $id
 * @property string $key
 * @property string $prefix
 * @property int $next_number
 */
class DocumentSequence extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'next_number' => 'integer',
    ];
}
