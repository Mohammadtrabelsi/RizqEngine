<?php

namespace App\Models;

use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A recurring monthly charge attached to a monthly budget, optionally carrying
 * a stored invoice/receipt file.
 *
 * @property float $amount
 * @property string|null $invoice_path
 */
class FixedPayment extends Model
{
    use TracksUserActions;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    /** @return BelongsTo<MonthlyBudget, $this> */
    public function monthlyBudget(): BelongsTo
    {
        return $this->belongsTo(MonthlyBudget::class);
    }

    public function hasInvoice(): bool
    {
        return ! empty($this->invoice_path);
    }
}
