@extends('layouts.app')

@section('title', 'Dashboard')

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

            {{-- Low stock list --}}
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="n-card-title mb-3">{{ __('general.low-stock') }}</div>
                        @forelse($low_stock_products->take(6) as $product)
                            <div class="d-flex justify-content-between align-items-center py-2"
                                 style="border-bottom:1px solid var(--color-divider)">
                                <div>
                                    <div style="font-size:13px; font-weight:500">{{ $product->product_name }}</div>
                                    <div class="n-meta">{{ $product->product_code }}</div>
                                </div>
                                <span class="n-tag n-tag-outline">{{ $product->product_quantity }} left</span>
                            </div>
                        @empty
                            <div class="n-meta py-2">{{ __('general.all-products-above-alert-level') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent transactions --}}
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="n-card-title mb-4">Recent transactions</div>
                        <div class="table-responsive">
                            <table class="table mb-0" style="width:100% !important">
                                <thead>
                                    <tr>
                                        <th>{{ __('general.order') }}</th>
                                        <th>{{ __('general.customer') }}</th>
                                        <th>{{ __('general.items') }}</th>
                                        <th>{{ __('general.total') }}</th>
                                        <th>{{ __('general.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recent_sales as $sale)
                                        @php
                                            $ps = $sale->payment_status;
                                            $tagClass = $ps === 'Paid' ? 'n-tag-success'
                                                : ($ps === 'Unpaid' ? 'n-tag-danger' : 'n-tag-neutral');
                                        @endphp
                                        <tr>
                                            <td>
                                                <a href="{{ route('sales.show', $sale->id) }}" style="color:var(--color-accent)">
                                                    {{ $sale->reference }}
                                                </a>
                                            </td>
                                            <td>{{ $sale->customer_name }}</td>
                                            <td>{{ $sale->sale_details_count }}</td>
                                            <td>{{ format_currency($sale->total_amount) }}</td>
                                            <td><span class="n-tag {{ $tagClass }}">{{ $ps }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="n-meta">{{ __('general.no-transactions') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
