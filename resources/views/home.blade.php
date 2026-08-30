@extends('layouts.app')

@section('title', __('general.dashboard'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item active">{{ __('general.dashboard') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid pb-5">

        {{-- Date range filter --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-white border rounded-lg shadow-sm">
                    <div class="card-body p-3 p-md-4">
                        <form method="GET" action="{{ route('home') }}" class="row g-3 align-items-end">
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <label for="from_date" class="form-label text-slate-600 small mb-1">{{ __('general.from-date') }}</label>
                                <input type="date" id="from_date" name="from_date" class="form-control input bg-white border-slate-300"
                                       value="{{ $from_date }}" max="{{ $to_date }}">
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <label for="to_date" class="form-label text-slate-600 small mb-1">{{ __('general.to-date') }}</label>
                                <input type="date" id="to_date" name="to_date" class="form-control input bg-white border-slate-300"
                                       value="{{ $to_date }}" min="{{ $from_date }}">
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-6 d-flex gap-2">
                                <button type="submit" class="btn btn-primary shadow-sm">{{ __('general.apply-filter') }}</button>
                                <a href="{{ route('home') }}" class="btn btn-secondary">{{ __('general.reset') }}</a>
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
                    ['label' => __('general.sales-today'),   'value' => format_currency($todays_sales),   'meta' => __('general.completed-sales-today'), 'icon' => 'bi-bag-check'],
                    ['label' => __('general.transactions'),    'value' => $todays_transactions,             'meta' => __('general.orders-today'),          'icon' => 'bi-receipt'],
                    ['label' => __('general.low-stock-items'), 'value' => $low_stock_products->count(),      'meta' => __('general.needs-reorder'),         'icon' => 'bi-exclamation-triangle'],
                    ['label' => __('general.todays-expenses'),'value' => format_currency($todays_expenses), 'meta' => __('general.logged-today'),        'icon' => 'bi-wallet2'],
                ];
            @endphp
            @foreach($kpis as $kpi)
                <div class="col-sm-6 col-xl-3">
                    <div class="card h-100 bg-white border rounded-lg shadow-sm p-3">
                        <div class="card-body p-0 d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="card-kicker text-uppercase font-weight-bold" style="color:#4f46e5; letter-spacing:0.08em; font-size:11px;">{{ $kpi['label'] }}</div>
                                    <div class="n-value font-weight-bold text-slate-900" style="font-size:28px; line-height:1.2; font-family:var(--font-heading);">{{ $kpi['value'] }}</div>
                                </div>
                                <div class="p-2 rounded-circle" style="background:#eef2ff; color:#4f46e5;">
                                    <i class="bi {{ $kpi['icon'] }} fs-4"></i>
                                </div>
                            </div>
                            <div class="card-meta text-slate-500 small mt-2 d-flex align-items-center gap-1">
                                <i class="bi bi-clock-history"></i> {{ $kpi['meta'] }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Financial summary --}}
        <div class="row g-4 mb-4">
            @php
                $summary = [
                    ['label' => __('general.revenue'),        'value' => format_currency($revenue),      'meta' => __('general.net-of-returns'),      'icon' => 'bi-cash-coin',    'tone' => 'text-indigo-600'],
                    ['label' => __('general.cost-of-goods'),  'value' => format_currency($cost_of_goods),'meta' => __('general.for-selected-period'), 'icon' => 'bi-box-seam',     'tone' => 'text-slate-500'],
                    ['label' => __('general.gross-profit'),   'value' => format_currency($profit),       'meta' => __('general.revenue-minus-cost'),  'icon' => 'bi-graph-up',     'tone' => $profit >= 0 ? 'text-emerald-600' : 'text-rose-600'],
                    ['label' => __('general.receivables'),    'value' => format_currency($receivables),  'meta' => __('general.due-from-customers'),  'icon' => 'bi-arrow-down-left-circle', 'tone' => 'text-emerald-600'],
                    ['label' => __('general.payables'),       'value' => format_currency($payables),     'meta' => __('general.due-to-suppliers'),    'icon' => 'bi-arrow-up-right-circle',  'tone' => 'text-rose-600'],
                ];
            @endphp
            @foreach($summary as $item)
                <div class="col-sm-6 col-xl">
                    <div class="card h-100 bg-white border rounded-lg shadow-sm p-3">
                        <div class="card-body p-0 d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="card-kicker text-uppercase font-weight-bold" style="color:#4f46e5; letter-spacing:0.08em; font-size:10px;">{{ $item['label'] }}</div>
                                    <div class="font-weight-bold text-slate-900 mt-1" style="font-size:22px; font-family:var(--font-heading);">{{ $item['value'] }}</div>
                                </div>
                                <i class="bi {{ $item['icon'] }} {{ $item['tone'] }} fs-4"></i>
                            </div>
                            <div class="card-meta text-slate-500 small mt-2">{{ $item['meta'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endcan

        <div class="row g-4 mb-4">
            {{-- Weekly sales bar chart --}}
            @can('show_weekly_sales_purchases')
            <div class="col-lg-7">
                <div class="card bg-white border rounded-lg shadow-sm h-100 p-4">
                    <div class="card-title font-weight-bold text-slate-900 h5 mb-4">{{ __('general.sales-this-week') }}</div>
                    <div class="n-bars mt-auto" style="display:flex; align-items:flex-end; gap:1rem; height:180px;">
                        @foreach($week_bars as $bar)
                            @php $pct = max(4, round(($bar['amount'] / ($week_max > 0 ? $week_max : 1)) * 100)); @endphp
                            <div class="n-bar-col flex-1 d-flex flex-column align-items-center gap-2" style="height:100%; justify-content:flex-end;">
                                <div class="n-bar rounded-top w-100" style="height: {{ $pct }}%; background:#4f46e5; max-width:36px;"
                                     title="{{ format_currency($bar['amount']) }}"></div>
                                <div class="card-meta text-slate-500 small">{{ $bar['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endcan

            @can('show_month_overview')
            <div class="col-lg-5">
                <div class="card bg-white border rounded-lg shadow-sm h-100 p-4">
                    <div class="card-header bg-transparent border-0 p-0 mb-3 font-weight-bold text-slate-900 h5">
                        {{ __('general.overview') }} this period
                    </div>
                    <div class="card-body p-0 d-flex justify-content-center align-items-center">
                        <div class="chart-container chart-container-sm" style="width:100%; max-width:320px;">
                            <canvas id="currentMonthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
        </div>

        {{-- Recent transactions --}}
        <div class="row g-4">
            <div class="col-12">
                <div class="card bg-white border rounded-lg shadow-sm p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <div class="card-title font-weight-bold text-slate-900 h5 mb-1">Recent transactions</div>
                            <div class="card-meta text-slate-500 small">Latest sales, invoices and payment status at a glance.</div>
                        </div>
                        <span class="tag tag-neutral bg-slate-100 text-slate-700 border-slate-200">{{ $recent_sales->count() }} {{ __('general.recent') }}</span>
                    </div>

                    <div class="row g-4">
                        @forelse($recent_sales as $sale)
                            @php
                                $ps = $sale->payment_status;
                                $tagStyle = $ps === 'Paid' ? 'background:#eef2ff; color:#4338ca; border:1px solid #c7d2fe;'
                                    : ($ps === 'Unpaid' ? 'background:#fef2f2; color:#b91c1c; border:1px solid #fecaca;' : 'background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;');
                            @endphp
                            <div class="col-xl-4 col-lg-6">
                                <div class="card bg-white border rounded-lg shadow-sm h-100 p-3">
                                    <div class="card-body p-0">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <a href="{{ route('sales.show', $sale->id) }}" class="font-weight-bold text-indigo-600 text-decoration-none">
                                                {{ $sale->reference }}
                                            </a>
                                            <span class="tag" style="{{ $tagStyle }}">{{ $ps }}</span>
                                        </div>
                                        <div class="d-flex flex-column gap-2 text-slate-600 small">
                                            <div class="d-flex justify-content-between border-bottom pb-1">
                                                <span>{{ __('general.customer') }}</span>
                                                <span class="text-slate-900 font-weight-bold">{{ $sale->customer_name }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between border-bottom pb-1">
                                                <span>{{ __('general.items') }}</span>
                                                <span class="text-slate-900">{{ $sale->sale_details_count }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between pt-1">
                                                <span>{{ __('general.total') }}</span>
                                                <span class="text-indigo-600 font-weight-bold">{{ format_currency($sale->total_amount) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-slate-500 text-center py-4">{{ __('general.no-transactions') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
