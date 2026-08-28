<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    /** Date-range filter — bound to the FROM/TO inputs in the toolbar. */
    public string $from;

    public string $to;

    public function mount(): void
    {
        $this->from = now()->startOfMonth()->toDateString();
        $this->to = now()->toDateString();
    }

    /** Quick-range chips (Today / 7 days / Month). */
    public function range(string $key): void
    {
        [$this->from, $this->to] = match ($key) {
            'today' => [now()->toDateString(), now()->toDateString()],
            '7days' => [now()->subDays(6)->toDateString(), now()->toDateString()],
            default => [now()->startOfMonth()->toDateString(), now()->toDateString()],
        };
    }

    public function resetRange(): void
    {
        $this->mount();
    }

    /**
     * KPI tiles. Replace the hard-coded figures with repository/aggregate
     * queries scoped to [$from, $to]. Shape kept stable for the Blade view.
     *
     * @return array<int, array{key:string, value:string, unit?:string, note:string, delta?:string, tone:string}>
     */
    public function stats(): array
    {
        return [
            ['key' => 'sales_today',  'value' => '2 480.90', 'unit' => 'DT', 'note' => __('pos.dash.sales_note', ['n' => 62]), 'delta' => '+8.4%', 'tone' => 'up'],
            ['key' => 'transactions', 'value' => '148',      'note' => __('pos.dash.tx_note', ['n' => 2]),                    'delta' => '—',     'tone' => 'flat'],
            ['key' => 'low_stock',    'value' => '1 002',    'note' => __('pos.dash.low_stock_note'),                        'delta' => 'ACTION','tone' => 'warn'],
            ['key' => 'expenses',     'value' => '318.00',   'unit' => 'DT', 'note' => __('pos.dash.expenses_note', ['n' => 9]), 'delta' => '-2.1%', 'tone' => 'flat'],
        ];
    }

    /** Revenue vs COGS bars for the last 14 days (paired heights, in px). */
    public function chart(): array
    {
        return [
            [42, 26], [66, 34], [54, 30], [88, 40], [72, 36], [104, 44], [62, 28],
            [80, 38], [96, 42], [58, 30], [112, 48], [86, 40], [124, 52], [100, 44],
        ];
    }

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('layouts.app', ['title' => __('pos.nav.dashboard')]);
    }
}
