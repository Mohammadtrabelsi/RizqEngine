<?php

namespace App\Models;

use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Model;

/**
 * A discrete outing ("bon de sortie") whose cost is itemized across the fixed
 * categories: food, gas, water, transport and miscellaneous. A voucher file is
 * generated and stored whenever the outing is created or updated.
 *
 * @property array<int, string>|null $participants
 * @property float $food
 * @property float $gas
 * @property float $water
 * @property float $transport
 * @property float $misc
 * @property string|null $voucher_path
 */
class Outing extends Model
{
    use TracksUserActions;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'participants' => 'array',
        'food' => 'decimal:2',
        'gas' => 'decimal:2',
        'water' => 'decimal:2',
        'transport' => 'decimal:2',
        'misc' => 'decimal:2',
    ];

    /** The itemized categories tracked for every outing. */
    public const CATEGORIES = ['food', 'gas', 'water', 'transport', 'misc'];

    public function total(): float
    {
        return (float) $this->food
            + (float) $this->gas
            + (float) $this->water
            + (float) $this->transport
            + (float) $this->misc;
    }

    public function hasVoucher(): bool
    {
        return ! empty($this->voucher_path);
    }
}
