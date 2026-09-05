<?php

namespace App\Services\Reports;

use App\Models\Expense;
use App\Models\Outing;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Builds a periodic "bilan" (balance) grouped by day, week or month.
 *
 * Each period row aggregates the four money flows of the business:
 *  - sales      (ventes)      : completed sales             [total_amount, cents]
 *  - purchases  (achats)      : completed purchases         [total_amount, cents]
 *  - outings    (sorties)     : itemized outing vouchers    [decimal columns]
 *  - expenses   (charges)     : recorded expenses           [amount, cents]
 *
 * The balance for a period is: sales - (purchases + outings + expenses).
 *
 * Monetary amounts are returned in the major currency unit; columns stored in
 * cents are divided by 100 here (outing columns are already in major units).
 */
class PeriodicSummaryService
{
    /** Supported grouping granularities. */
    public const GROUPINGS = ['day', 'week', 'month'];

    /**
     * Build the periodic summary. Returns an ordered list of period rows plus a
     * grand-total row covering the whole range.
     *
     * @return array{rows: array<int, array<string, mixed>>, totals: array<string, float>}
     */
    public function summary(?string $startDate, ?string $endDate, string $grouping): array
    {
        $grouping = in_array($grouping, self::GROUPINGS, true) ? $grouping : 'day';

        $sales = $this->bucketize(
            $this->fetchAmounts(Sale::completed(), 'total_amount', 100),
            $grouping
        );
        $purchases = $this->bucketize(
            $this->fetchAmounts(Purchase::completed(), 'total_amount', 100),
            $grouping
        );
        $expenses = $this->bucketize(
            $this->fetchAmounts(Expense::query(), 'amount', 100),
            $grouping
        );
        $outings = $this->bucketize(
            $this->fetchOutingAmounts(),
            $grouping
        );

        $keys = collect([$sales, $purchases, $expenses, $outings])
            ->flatMap(fn (array $bucket) => array_keys($bucket))
            ->unique()
            ->sortDesc()
            ->values();

        $rows = [];
        $totals = ['sales' => 0.0, 'purchases' => 0.0, 'outings' => 0.0, 'expenses' => 0.0, 'balance' => 0.0];

        foreach ($keys as $key) {
            if (! $this->keyInRange($key, $startDate, $endDate, $grouping)) {
                continue;
            }

            $salesAmount = $sales[$key] ?? 0.0;
            $purchasesAmount = $purchases[$key] ?? 0.0;
            $outingsAmount = $outings[$key] ?? 0.0;
            $expensesAmount = $expenses[$key] ?? 0.0;
            $balance = $salesAmount - ($purchasesAmount + $outingsAmount + $expensesAmount);

            $rows[] = [
                'key' => $key,
                'label' => $this->label($key, $grouping),
                'sales' => $salesAmount,
                'purchases' => $purchasesAmount,
                'outings' => $outingsAmount,
                'expenses' => $expensesAmount,
                'balance' => $balance,
            ];

            $totals['sales'] += $salesAmount;
            $totals['purchases'] += $purchasesAmount;
            $totals['outings'] += $outingsAmount;
            $totals['expenses'] += $expensesAmount;
            $totals['balance'] += $balance;
        }

        return ['rows' => $rows, 'totals' => $totals];
    }

    /**
     * Fetch (date, amount) pairs for a model query, scaling the amount column.
     *
     * @return Collection<int, array{date: string, amount: float}>
     */
    protected function fetchAmounts($query, string $amountColumn, int $divisor): Collection
    {
        return $query
            ->get(['date', $amountColumn])
            ->map(fn ($row) => [
                'date' => (string) $row->getRawOriginal('date'),
                'amount' => (float) $row->getAttributes()[$amountColumn] / $divisor,
            ]);
    }

    /**
     * Fetch (date, amount) pairs for outings, summing the itemized categories.
     *
     * @return Collection<int, array{date: string, amount: float}>
     */
    protected function fetchOutingAmounts(): Collection
    {
        $columns = array_merge(['date'], Outing::CATEGORIES);

        return Outing::query()
            ->get($columns)
            ->map(fn (Outing $outing) => [
                'date' => (string) $outing->getRawOriginal('date'),
                'amount' => $outing->total(),
            ]);
    }

    /**
     * Group amounts into period buckets keyed by the period key.
     *
     * @param  Collection<int, array{date: string, amount: float}>  $items
     * @return array<string, float>
     */
    protected function bucketize(Collection $items, string $grouping): array
    {
        $buckets = [];

        foreach ($items as $item) {
            if (empty($item['date'])) {
                continue;
            }

            $key = $this->periodKey(Carbon::parse($item['date']), $grouping);
            $buckets[$key] = ($buckets[$key] ?? 0.0) + $item['amount'];
        }

        return $buckets;
    }

    /**
     * Compute the period key for a date under the given grouping.
     */
    protected function periodKey(Carbon $date, string $grouping): string
    {
        return match ($grouping) {
            'month' => $date->format('Y-m'),
            'week' => $date->format('o-\WW'),
            default => $date->format('Y-m-d'),
        };
    }

    /**
     * Whether a period key falls within the (optional) start/end date range.
     */
    protected function keyInRange(string $key, ?string $startDate, ?string $endDate, string $grouping): bool
    {
        [$periodStart, $periodEnd] = $this->keyBounds($key, $grouping);

        if ($startDate && $periodEnd->lt(Carbon::parse($startDate)->startOfDay())) {
            return false;
        }

        if ($endDate && $periodStart->gt(Carbon::parse($endDate)->endOfDay())) {
            return false;
        }

        return true;
    }

    /**
     * The [start, end] Carbon bounds covered by a period key.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function keyBounds(string $key, string $grouping): array
    {
        return match ($grouping) {
            'month' => [
                Carbon::createFromFormat('Y-m-d', $key.'-01')->startOfMonth(),
                Carbon::createFromFormat('Y-m-d', $key.'-01')->endOfMonth(),
            ],
            'week' => [
                Carbon::now()->setISODate((int) substr($key, 0, 4), (int) substr($key, 6))->startOfWeek(),
                Carbon::now()->setISODate((int) substr($key, 0, 4), (int) substr($key, 6))->endOfWeek(),
            ],
            default => [
                Carbon::parse($key)->startOfDay(),
                Carbon::parse($key)->endOfDay(),
            ],
        };
    }

    /**
     * Human-readable label for a period key.
     */
    protected function label(string $key, string $grouping): string
    {
        return match ($grouping) {
            'month' => Carbon::createFromFormat('Y-m-d', $key.'-01')->translatedFormat('F Y'),
            'week' => $this->weekLabel($key),
            default => Carbon::parse($key)->translatedFormat('d M Y'),
        };
    }

    /**
     * Label for a week key such as "W32 · 04 Aug - 10 Aug 2025".
     */
    protected function weekLabel(string $key): string
    {
        [$start, $end] = $this->keyBounds($key, 'week');
        $week = (int) substr($key, 6);

        return sprintf(
            'S%02d · %s - %s',
            $week,
            $start->translatedFormat('d M'),
            $end->translatedFormat('d M Y')
        );
    }
}
