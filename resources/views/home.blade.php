@extends('layouts.app')

@section('title', __('general.dashboard'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item active">{{ __('general.dashboard') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Date range filter --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="{{ route('home') }}" class="row g-3 align-items-end">
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <label for="from_date" class="form-label n-meta mb-1">{{ __('general.from-date') }}</label>
                                <input type="date" id="from_date" name="from_date" class="form-control"
                                       value="{{ $from_date }}" max="{{ $to_date }}">
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <label for="to_date" class="form-label n-meta mb-1">{{ __('general.to-date') }}</label>
                                <input type="date" id="to_date" name="to_date" class="form-control"
                                       value="{{ $to_date }}" min="{{ $from_date }}">
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-6 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">{{ __('general.apply-filter') }}</button>
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary">{{ __('general.reset') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI tiles --}}
        @can('show_total_stats')
        <div class="row g-4 mb-4">
            @php
                $kpis = [
                    ['label' => __('general.sales-today'),   'value' => format_currency($todays_sales),   'meta' => __('general.completed-sales-today')],
                    ['label' => __('general.transactions'),    'value' => $todays_transactions,             'meta' => __('general.orders-today')],
                    ['label' => __('general.low-stock-items'), 'value' => $low_stock_products->count(),      'meta' => __('general.needs-reorder')],
                    ['label' => __('general.todays-expenses'),'value' => format_currency($todays_expenses), 'meta' => __('general.logged-today')],
                ];
            @endphp
            @foreach($kpis as $kpi)
                <div class="col-sm-6 col-xl-3">
                    <div class="card h-100 shadow-sm n-stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="n-card-kicker">{{ $kpi['label'] }}</div>
                                    <div class="n-value">{{ $kpi['value'] }}</div>
                                </div>
                                <i class="bi bi-graph-up-arrow text-accent fs-3"></i>
                            </div>
                            <div class="n-meta mt-3">{{ $kpi['meta'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Financial summary --}}
        <div class="row g-4 mb-4">
            @php
                $summary = [
                    ['label' => __('general.revenue'),        'value' => format_currency($revenue),      'meta' => __('general.net-of-returns'),      'icon' => 'bi-cash-coin',    'tone' => 'text-accent'],
                    ['label' => __('general.cost-of-goods'),  'value' => format_currency($cost_of_goods),'meta' => __('general.for-selected-period'), 'icon' => 'bi-box-seam',     'tone' => 'text-muted'],
                    ['label' => __('general.gross-profit'),   'value' => format_currency($profit),       'meta' => __('general.revenue-minus-cost'),  'icon' => 'bi-graph-up',     'tone' => $profit >= 0 ? 'text-success' : 'text-danger'],
                    ['label' => __('general.receivables'),    'value' => format_currency($receivables),  'meta' => __('general.due-from-customers'),  'icon' => 'bi-arrow-down-left-circle', 'tone' => 'text-success'],
                    ['label' => __('general.payables'),       'value' => format_currency($payables),     'meta' => __('general.due-to-suppliers'),    'icon' => 'bi-arrow-up-right-circle',  'tone' => 'text-danger'],
                ];
            @endphp
            @foreach($summary as $item)
                <div class="col-sm-6 col-xl">
                    <div class="card h-100 shadow-sm n-stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="n-card-kicker">{{ $item['label'] }}</div>
                                    <div class="n-value">{{ $item['value'] }}</div>
                                </div>
                                <i class="bi {{ $item['icon'] }} {{ $item['tone'] }} fs-3"></i>
                            </div>
                            <div class="n-meta mt-3">{{ $item['meta'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endcan

        <div class="row gy-4">
            {{-- Weekly sales bar chart --}}
            @can('show_weekly_sales_purchases')
            <div class="col-lg-7">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="n-card-title mb-4">{{ __('general.sales-this-week') }}</div>
                        <div class="n-bars">
                            @foreach($week_bars as $bar)
                                @php $pct = max(2, round(($bar['amount'] / $week_max) * 100)); @endphp
                                <div class="n-bar-col">
                                    <div class="n-bar" style="height: {{ $pct }}%"
                                         title="{{ format_currency($bar['amount']) }}"></div>
                                    <div class="n-meta">{{ $bar['label'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endcan
            @can('show_month_overview')
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header">
                        {{ __('general.overview') }} this period
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <div class="chart-container chart-container-sm">
                            <canvas id="currentMonthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
        </div>

        {{-- Recent transactions --}}
        <div class="row gy-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <div class="n-card-title mb-1">Recent transactions</div>
                                <div class="n-meta">Latest sales, invoices and payment status at a glance.</div>
                            </div>
                            <span class="badge badge-pill badge-secondary">{{ $recent_sales->count() }} {{ __('general.recent') }}</span>
                        </div>

                        <div class="row g-4">
                            @forelse($recent_sales as $sale)
                                @php
                                    $ps = $sale->payment_status;
                                    $tagClass = $ps === 'Paid' ? 'n-tag-success'
                                        : ($ps === 'Unpaid' ? 'n-tag-danger' : 'n-tag-neutral');
                                @endphp
                                <div class="col-xl-4 col-lg-6">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <a href="{{ route('sales.show', $sale->id) }}" class="font-weight-bold text-accent">
                                                    {{ $sale->reference }}
                                                </a>
                                                <span class="n-tag {{ $tagClass }}">{{ $ps }}</span>
                                            </div>
                                            <ul class="list-group list-group-flush n-transaction-list mb-0">
                                                <li class="list-group-item d-flex justify-content-between px-0 py-2"><span>{{ __('general.customer') }}</span><span>{{ $sale->customer_name }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0 py-2"><span>{{ __('general.items') }}</span><span>{{ $sale->sale_details_count }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0 py-2"><span>{{ __('general.total') }}</span><span>{{ format_currency($sale->total_amount) }}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 n-meta">{{ __('general.no-transactions') }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
