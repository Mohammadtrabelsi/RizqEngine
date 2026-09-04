<?php

namespace App\Models;

use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A dedicated monthly financial envelope. The remaining balance is always
 * derived from the starting budget minus the month's fixed payments and the
 * aggregate of every outing that falls within the calendar month.
 *
 * @property int $year
 * @property int $month
 * @property float $starting_budget
 */
class MonthlyBudget extends Model
{
    use TracksUserActions;

    protected $guarded = [];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'starting_budget' => 'decimal:2',
    ];

    /** @return HasMany<FixedPayment, $this> */
    public function fixedPayments(): HasMany
    {
        return $this->hasMany(FixedPayment::class);
    }

    /**
     * Outings are matched to the budget by calendar month rather than a foreign
     * key: a single outing table is filtered by year/month on demand.
     *
     * @return Builder<Outing>
     */
    public function outingsQuery()
    {
        return Outing::query()->whereYear('date', $this->year)->whereMonth('date', $this->month);
    }

    public function totalFixedPayments(): float
    {
        return (float) $this->fixedPayments()->sum('amount');
    }

    public function totalOutings(): float
    {
        return (float) $this->outingsQuery()->get()->sum(fn (Outing $outing) => $outing->total());
    }

    public function totalExpenses(): float
    {
        return $this->totalFixedPayments() + $this->totalOutings();
    }

    public function remainingBalance(): float
    {
        return (float) $this->starting_budget - $this->totalExpenses();
    }

    public function label(): string
    {
        return Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y');
    }

    public function periodStart(): Carbon
    {
        return Carbon::create($this->year, $this->month, 1)->startOfMonth();
    }

    public function periodEnd(): Carbon
    {
        return Carbon::create($this->year, $this->month, 1)->endOfMonth();
    }
}
