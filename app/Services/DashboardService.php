<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Owns every query that feeds the admin dashboard, keeping Eloquent and the
 * raw reporting SQL out of the {@see \App\Livewire\Dashboard} component.
 *
 * Monetary columns are stored as integer cents; this service converts them to
 * major units so the component only deals with display-ready values.
 *
 * @phpstan-type Range array{0: string, 1: string}
 */
class DashboardService
{
    /**
     * Completed-sales revenue within the range, in major units.
     *
     * @param  Range  $range
     */
    public function salesTotal(array $range): float
    {
        return (float) Sale::completed()->whereBetween('date', $range)->sum('total_amount') / 100;
    }

    /**
     * @param  Range  $range
     */
    public function salesCount(array $range): int
    {
        return Sale::completed()->whereBetween('date', $range)->count();
    }

    /**
     * @param  Range  $range
     */
    public function transactionCount(array $range): int
    {
        return Sale::whereBetween('date', $range)->count();
    }

    public function lowStockCount(): int
    {
        return Product::whereColumn('product_quantity', '<=', 'product_stock_alert')->count();
    }

    /**
     * @param  Range  $range
     */
    public function expensesTotal(array $range): float
    {
        return (float) Expense::whereBetween('date', $range)->sum('amount') / 100;
    }

    /**
     * @param  Range  $range
     */
    public function expenseCount(array $range): int
    {
        return Expense::whereBetween('date', $range)->count();
    }

    public function pendingOrders(): int
    {
        if (! class_exists(Commande::class)) {
            return 0;
        }

        return (int) Commande::query()->count();
    }

    public function unreadNotificationCount(): int
    {
        if (! Schema::hasTable('notifications')) {
            return 0;
        }

        return (int) (auth()->user()?->unreadNotifications()->count() ?? 0);
    }

    /**
     * 14 daily revenue/COGS pairs, pre-scaled to pixels for the pure-CSS chart.
     * Revenue tops out at 124px, COGS at 52px.
     *
     * @return Collection<int, array{label: string, revenue: float, cogs: float, revenue_px: int, cogs_px: int}>
     */
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

    public function receivables(): float
    {
        return (float) Sale::completed()->sum('due_amount') / 100;
    }

    public function debtorCount(): int
    {
        return Sale::completed()->where('due_amount', '>', 0)->count();
    }

    public function supplierDebt(): float
    {
        return (float) Purchase::completed()->sum('due_amount') / 100;
    }

    /**
     * @return Collection<int, array{reference: string, customer: string, status: string, total: float}>
     */
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

    /**
     * @return Collection<int, array{name: string, reorder_point: int, stock: int}>
     */
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

    protected function paymentStatusKey(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'paid' => 'paid',
            'partial' => 'partial',
            'returned', 'return' => 'return',
            default => 'draft',
        };
    }
}
