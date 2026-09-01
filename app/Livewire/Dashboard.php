<?php

namespace App\Livewire;

use App\Services\DashboardService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * @property-read float $salesToday
 * @property-read int $salesCount
 * @property-read int $txCount
 * @property-read int $lowStockCount
 * @property-read float $expensesToday
 * @property-read int $expenseCount
 * @property-read int $registerCount
 * @property-read int $pendingOrders
 * @property-read int $unreadCount
 * @property-read string $salesDeltaLabel
 * @property-read string $expensesDeltaLabel
 * @property-read Collection<int, array{label: string, revenue: float, cogs: float, revenue_px: int, cogs_px: int}> $series
 * @property-read float $revenueTotal
 * @property-read float $cogsTotal
 * @property-read float $grossProfit
 * @property-read int $marginPct
 * @property-read float $receivables
 * @property-read int $debtorCount
 * @property-read float $supplierDebt
 * @property-read string $nextDueDate
 * @property-read Collection<int, array{reference: string, customer: string, status: string, total: float}> $recentTransactions
 * @property-read Collection<int, array{name: string, reorder_point: int, stock: int}> $restockQueue
 */
class Dashboard extends Component
{
    #[Url]
    public string $from = '';

    #[Url]
    public string $to = '';

    #[Url]
    public string $preset = 'today';

    public function mount(): void
    {
        if ($this->from === '' || $this->to === '') {
            $this->setPreset($this->preset ?: 'today');
        }
    }

    public function setPreset(string $preset): void
    {
        $today = CarbonImmutable::today();

        [$from, $to] = match ($preset) {
            '7d' => [$today->subDays(6), $today],
            'month' => [$today->startOfMonth(), $today->endOfMonth()],
            default => [$today, $today],
        };

        $this->preset = $preset;
        $this->from = $from->toDateString();
        $this->to = $to->toDateString();
    }

    public function apply(): void
    {
        $this->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $this->preset = 'custom';
    }

    public function resetRange(): void
    {
        $this->setPreset('today');
    }

    /** @return array{0: string, 1: string} SQL-comparable datetime bounds. */
    protected function range(): array
    {
        [$from, $to] = [$this->from, $this->to];

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        return [$from.' 00:00:00', $to.' 23:59:59'];
    }

    /** The immediately preceding range of equal length, for delta chips. */
    protected function previousRange(): array
    {
        $from = CarbonImmutable::parse($this->from);
        $to = CarbonImmutable::parse($this->to);
        $days = $from->diffInDays($to) + 1;

        return [
            $from->subDays($days)->toDateString().' 00:00:00',
            $to->subDays($days)->toDateString().' 23:59:59',
        ];
    }

    #[Computed]
    public function salesToday(DashboardService $dashboard): float
    {
        return $dashboard->salesTotal($this->range());
    }

    #[Computed]
    public function salesCount(DashboardService $dashboard): int
    {
        return $dashboard->salesCount($this->range());
    }

    #[Computed]
    public function txCount(DashboardService $dashboard): int
    {
        return $dashboard->transactionCount($this->range());
    }

    #[Computed]
    public function lowStockCount(DashboardService $dashboard): int
    {
        return $dashboard->lowStockCount();
    }

    #[Computed]
    public function expensesToday(DashboardService $dashboard): float
    {
        return $dashboard->expensesTotal($this->range());
    }

    #[Computed]
    public function expenseCount(DashboardService $dashboard): int
    {
        return $dashboard->expenseCount($this->range());
    }

    #[Computed]
    public function registerCount(): int
    {
        return 1;
    }

    #[Computed]
    public function pendingOrders(DashboardService $dashboard): int
    {
        return $dashboard->pendingOrders();
    }

    #[Computed]
    public function unreadCount(DashboardService $dashboard): int
    {
        return $dashboard->unreadNotificationCount();
    }

    #[Computed]
    public function salesDeltaLabel(DashboardService $dashboard): string
    {
        return $this->deltaLabel($this->salesToday, $dashboard->salesTotal($this->previousRange()));
    }

    #[Computed]
    public function expensesDeltaLabel(DashboardService $dashboard): string
    {
        return $this->deltaLabel($this->expensesToday, $dashboard->expensesTotal($this->previousRange()));
    }

    protected function deltaLabel(float $current, float $previous): string
    {
        if ($previous <= 0.0) {
            return '—';
        }

        $pct = ($current - $previous) / $previous * 100;
        $sign = $pct >= 0 ? '+' : '';

        return $sign.number_format($pct, 1).'%';
    }

    /**
     * 14 stacked bar pairs (revenue on top, COGS below), pre-scaled to pixels
     * for the pure-CSS chart.
     *
     * @return Collection<int, array{label: string, revenue: float, cogs: float, revenue_px: int, cogs_px: int}>
     */
    #[Computed]
    public function series(DashboardService $dashboard): Collection
    {
        return $dashboard->series();
    }

    #[Computed]
    public function revenueTotal(): float
    {
        return (float) $this->series->sum('revenue');
    }

    #[Computed]
    public function cogsTotal(): float
    {
        return (float) $this->series->sum('cogs');
    }

    #[Computed]
    public function grossProfit(): float
    {
        return $this->revenueTotal - $this->cogsTotal;
    }

    #[Computed]
    public function marginPct(): int
    {
        return $this->revenueTotal > 0 ? (int) round($this->grossProfit / $this->revenueTotal * 100) : 0;
    }

    #[Computed]
    public function receivables(DashboardService $dashboard): float
    {
        return $dashboard->receivables();
    }

    #[Computed]
    public function debtorCount(DashboardService $dashboard): int
    {
        return $dashboard->debtorCount();
    }

    #[Computed]
    public function supplierDebt(DashboardService $dashboard): float
    {
        return $dashboard->supplierDebt();
    }

    #[Computed]
    public function nextDueDate(): string
    {
        return CarbonImmutable::today()->addDays(7)->isoFormat('D MMM');
    }

    /**
     * @return Collection<int, array{reference: string, customer: string, status: string, total: float}>
     */
    #[Computed]
    public function recentTransactions(DashboardService $dashboard): Collection
    {
        return $dashboard->recentTransactions();
    }

    /**
     * @return Collection<int, array{name: string, reorder_point: int, stock: int}>
     */
    #[Computed]
    public function restockQueue(DashboardService $dashboard): Collection
    {
        return $dashboard->restockQueue();
    }

    public function money(float|int $value): string
    {
        return number_format((float) $value, 2, '.', ' ').'DT';
    }

    public function formatInt(int $value): string
    {
        return number_format($value, 0, '.', ' ');
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('components.layouts.admin', [
            'title' => __('dash.title'),
        ]);
    }
}
