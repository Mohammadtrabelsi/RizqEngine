@extends('layouts.app')

@section('title', __('general.dashboard'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item active">{{ __('general.dashboard') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- KPI tiles --}}
        @can('show_total_stats')
        <div class="row">
            @php
                $kpis = [
                    ['label' => __('general.sales-today'),   'value' => format_currency($todays_sales),   'meta' => __('general.completed-sales-today')],
                       ['label' => __('general.transactions'),    'value' => $todays_transactions,             'meta' => __('general.orders-today')],
                    ['label' => __('general.low-stock-items'), 'value' => $low_stock_products->count(),      'meta' => __('general.needs-reorder')],
                    ['label' => __('general.todays-expenses'),'value' => format_currency($todays_expenses), 'meta' => __('general.logged-today')],
                ];
            @endphp
            @foreach($kpis as $kpi)
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="n-card-kicker">{{ $kpi['label'] }}</div>
                            <div class="n-value" style="font-size:26px; margin:6px 0 4px;">{{ $kpi['value'] }}</div>
                            <div class="n-meta">{{ $kpi['meta'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endcan

        <div class="row">
            {{-- Weekly sales bar chart --}}
            @can('show_weekly_sales_purchases')
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
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
                        {{ __('general.overview') }} of {{ now()->format('F, Y') }}
                    </div>
                    <div class="card-body d-flex justify-content-center">
                        <div class="chart-container chart-container-sm">
                            <canvas id="currentMonthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
        </div>

        {{-- Recent transactions --}}
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="n-card-title mb-4">Recent transactions</div>
                        <div class="table-responsive">
                            <div class="row">
                                @forelse($recent_sales as $sale)
                                    @php
                                        $ps = $sale->payment_status;
                                        $tagClass = $ps === 'Paid' ? 'n-tag-success'
                                            : ($ps === 'Unpaid' ? 'n-tag-danger' : 'n-tag-neutral');
                                    @endphp
                                    <div class="col-xl-4 col-lg-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <a href="{{ route('sales.show', $sale->id) }}" style="color:var(--color-accent)">
                                                        {{ $sale->reference }}
                                                    </a>
                                                    <span class="n-tag {{ $tagClass }}">{{ $ps }}</span>
                                                </div>
                                                <ul class="list-group list-group-flush mb-0">
                                                    <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('general.customer') }}</span><span>{{ $sale->customer_name }}</span></li>
                                                    <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('general.items') }}</span><span>{{ $sale->sale_details_count }}</span></li>
                                                    <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('general.total') }}</span><span>{{ format_currency($sale->total_amount) }}</span></li>
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

    </div>
@endsection
