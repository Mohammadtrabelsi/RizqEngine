<?php

namespace App\Livewire;

use App\Models\Commande;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
    public function salesToday(): float
    {
        return (float) Sale::completed()->whereBetween('date', $this->range())->sum('total_amount') / 100;
    }

    #[Computed]
    public function salesCount(): int
    {
        return Sale::completed()->whereBetween('date', $this->range())->count();
    }

    #[Computed]
    public function txCount(): int
    {
        return Sale::whereBetween('date', $this->range())->count();
    }

    #[Computed]
    public function lowStockCount(): int
    {
        return Product::whereColumn('product_quantity', '<=', 'product_stock_alert')->count();
    }

    #[Computed]
    public function expensesToday(): float
    {
        return (float) Expense::whereBetween('date', $this->range())->sum('amount') / 100;
    }

    #[Computed]
    public function expenseCount(): int
    {
        return Expense::whereBetween('date', $this->range())->count();
    }

    #[Computed]
    public function registerCount(): int
    {
        return 1;
    }

    #[Computed]
    public function pendingOrders(): int
    {
        if (! class_exists(Commande::class)) {
            return 0;
        }

        return (int) Commande::query()->count();
    }

    #[Computed]
    public function unreadCount(): int
    {
        if (! Schema::hasTable('notifications')) {
            return 0;
        }

        return (int) (auth()->user()?->unreadNotifications()->count() ?? 0);
    }

    #[Computed]
    public function salesDeltaLabel(): string
    {
        $current = $this->salesToday;
        $previous = (float) Sale::completed()->whereBetween('date', $this->previousRange())->sum('total_amount') / 100;

        return $this->deltaLabel($current, $previous);
    }

    #[Computed]
    public function expensesDeltaLabel(): string
    {
        $current = $this->expensesToday;
        $previous = (float) Expense::whereBetween('date', $this->previousRange())->sum('amount') / 100;

        return $this->deltaLabel($current, $previous);
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
     * for the pure-CSS chart. Revenue tops out at 124px, COGS at 52px.
     *
     * @return Collection<int, array{label: string, revenue: float, cogs: float, revenue_px: int, cogs_px: int}>
     */
    #[Computed]
    public function series(): Collection
    {
        $start = CarbonImmutable::today()->subDays(13);
        $end = CarbonImmutable::today();
        $window = [$start->toDateString().' 00:00:00', $end->toDateString().' 23:59:59'];

        $revenueByDay = Sale::completed()
            ->whereBetween('date', $window)
            ->groupBy(DB::raw('DATE(date)'))
            ->selectRaw('DATE(date) as day, SUM(total_amount) as amount')
            ->pluck('amount', 'day');

        $cogsByDay = DB::table('sale_details')
            ->join('sales', 'sales.id', '=', 'sale_details.sale_id')
            ->join('products', 'products.id', '=', 'sale_details.product_id')
            ->where('sales.status', 'Completed')
            ->whereBetween('sales.date', $window)
            ->groupBy(DB::raw('DATE(sales.date)'))
            ->selectRaw('DATE(sales.date) as day, SUM(sale_details.quantity * products.product_cost) as amount')
            ->pluck('amount', 'day');

        /** @var list<array{label: string, revenue: float, cogs: float}> $rows */
        $rows = [];
        for ($d = $start; $d->lessThanOrEqualTo($end); $d = $d->addDay()) {
            $key = $d->toDateString();
            $rows[] = [
                'label' => $d->isoFormat('D MMM'),
                'revenue' => (float) ($revenueByDay[$key] ?? 0) / 100,
                'cogs' => (float) ($cogsByDay[$key] ?? 0) / 100,
            ];
        }

        $max = max(collect($rows)->max('revenue') ?: 1, 1);

        return collect($rows)->map(fn (array $row) => [
            'label' => $row['label'],
            'revenue' => $row['revenue'],
            'cogs' => $row['cogs'],
            'revenue_px' => (int) round($row['revenue'] / $max * 124),
            'cogs_px' => (int) round(min($row['cogs'], $max) / $max * 52),
        ]);
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
    public function receivables(): float
    {
        return (float) Sale::completed()->sum('due_amount') / 100;
    }

    #[Computed]
    public function debtorCount(): int
    {
        return Sale::completed()->where('due_amount', '>', 0)->count();
    }

    #[Computed]
    public function supplierDebt(): float
    {
        return (float) Purchase::completed()->sum('due_amount') / 100;
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
    public function recentTransactions(): Collection
    {
        return Sale::latest()
            ->limit(5)
            ->get(['id', 'reference', 'customer_name', 'total_amount', 'status', 'payment_status'])
            ->map(fn (Sale $sale) => [
                'reference' => $sale->reference,
                'customer' => (string) ($sale->customer_name ?: __('dash.walk_in')),
                'status' => $this->paymentStatusKey($sale->payment_status),
                'total' => (float) $sale->total_amount,
            ]);
    }

    protected function paymentStatusKey(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'paid' => 'paid',
            'partial' => 'partial',
            'returned', 'return' => 'return',
            default => 'draft',
        };
    }

    /**
     * @return Collection<int, array{name: string, reorder_point: int, stock: int}>
     */
    #[Computed]
    public function restockQueue(): Collection
    {
        return Product::whereColumn('product_quantity', '<=', 'product_stock_alert')
            ->orderBy('product_quantity')
            ->limit(4)
            ->get(['id', 'product_name', 'product_quantity', 'product_stock_alert'])
            ->map(fn (Product $product) => [
                'name' => $product->product_name,
                'reorder_point' => (int) $product->product_stock_alert,
                'stock' => (int) $product->product_quantity,
            ]);
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
        return view('livewire.dashboard')->layout('layouts.redesign');
    }
}
