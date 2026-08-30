<?php

// app/Livewire/Dashboard.php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

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
        $this->setPreset($this->preset ?: 'today');
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
        unset($this->salesToday, $this->series); // bust computed caches
    }

    public function resetRange(): void
    {
        $this->setPreset('today');
    }

    protected function range(): array
    {
        return [
            CarbonImmutable::parse($this->from)->startOfDay(),
            CarbonImmutable::parse($this->to)->endOfDay(),
        ];
    }

    #[Computed]
    public function salesToday(): float
    {
        [$from, $to] = $this->range();

        return (float) Transaction::sales()->whereBetween('created_at', [$from, $to])->sum('total');
    }

    #[Computed]
    public function salesCount(): int
    {
        [$from, $to] = $this->range();

        return Transaction::sales()->whereBetween('created_at', [$from, $to])->count();
    }

    #[Computed]
    public function txCount(): int
    {
        [$from, $to] = $this->range();

        return Transaction::whereBetween('created_at', [$from, $to])->count();
    }

    #[Computed]
    public function lowStockCount(): int
    {
        return Product::lowStock()->count();
    }

    #[Computed]
    public function expensesToday(): float
    {
        [$from, $to] = $this->range();

        return (float) Expense::whereBetween('spent_at', [$from, $to])->sum('amount');
    }

    /** 14 stacked bar pairs, pre-scaled to pixels for the CSS chart. */
    #[Computed]
    public function series(): Collection
    {
        $days = Transaction::dailyTotals(CarbonImmutable::today()->subDays(13), CarbonImmutable::today());
        $max = max($days->max('revenue') ?: 1, 1);

        return $days->map(fn (array $d) => [
            'label' => CarbonImmutable::parse($d['date'])->isoFormat('D MMM'),
            'revenue_px' => (int) round($d['revenue'] / $max * 124),
            'cogs_px' => (int) round($d['cogs'] / $max * 52),
        ]);
    }

    #[Computed]
    public function revenueTotal(): float
    {
        return (float) $this->series->sum('revenue');
    }

    #[Computed]
    public function grossProfit(): float
    {
        return $this->revenueTotal - $this->cogsTotal();
    }

    #[Computed]
    public function marginPct(): int
    {
        return $this->revenueTotal > 0 ? (int) round($this->grossProfit / $this->revenueTotal * 100) : 0;
    }

    #[Computed]
    public function recentTransactions()
    {
        return Transaction::latest()->limit(5)->get();
    }

    #[Computed]
    public function restockQueue()
    {
        return Product::lowStock()->orderBy('stock')->limit(4)->get();
    }

    public function money(float|int $value): string
    {
        return number_format($value, 2, '.', ' ').'DT';
    }

    public function formatInt(int $value): string
    {
        return number_format($value, 0, '.', ' ');
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app');
    }
}
