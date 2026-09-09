@extends('layouts.app')

@section('title', __('stockexit.exit_details'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('stock-exits.index') }}">{{ __('stockexit.stock_exits') }}</a></li>
        <li class="breadcrumb-item active">{{ $stockExit->reference }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        @include('utils.alerts')
        <div class="card">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex flex-wrap align-items-center">
                <div>
                    {{ __('stockexit.bon_de_sortie') }} — {{ __('stockexit.reference') }}: <strong>{{ $stockExit->reference }}</strong>
                    @include('stockexit.partials.status', ['data' => $stockExit])
                </div>
                <div class="mfs-auto d-print-none">
                    @can('create_stock_entries')
                        @if($stockExit->status !== \App\Models\StockExit::STATUS_CLOSED)
                            <a href="{{ route('stock-entries.create', $stockExit->id) }}" class="btn btn-sm btn-success">
                                <i class="bi bi-box-arrow-in-down"></i> {{ __('stockexit.declare_return') }}
                            </a>
                        @endif
                    @endcan
                </div>
            </div>
            <div class="flex-auto p-2">
                <div class="row mb-4">
                    <div class="col-md-3"><span class="fw-bold d-block">{{ __('stockexit.date') }}</span>{{ \Illuminate\Support\Carbon::parse($stockExit->date)->format('d M, Y') }}</div>
                    <div class="col-md-3"><span class="fw-bold d-block">{{ __('stockexit.reason') }}</span>{{ $stockExit->reason ?: '—' }}</div>
                    <div class="col-md-3"><span class="fw-bold d-block">{{ __('stockexit.destination') }}</span>{{ $stockExit->destination ?: '—' }}</div>
                    <div class="col-md-3"><span class="fw-bold d-block">{{ __('stockexit.responsible') }}</span>{{ $stockExit->responsible ?: '—' }}</div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3"><span class="fw-bold d-block">{{ __('stockexit.driver') }}</span>{{ $stockExit->driver?->name ?: '—' }}</div>
                    <div class="col-md-3"><span class="fw-bold d-block">{{ __('stockexit.vehicle') }}</span>{{ $stockExit->vehicle?->label ?: '—' }}</div>
                    <div class="col-md-3"><span class="fw-bold d-block">{{ __('stockexit.kind') }}</span>{{ $stockExit->isConsignment() ? __('stockexit.kind_consignment') : __('stockexit.kind_standard') }}</div>
                    @if($stockExit->isConsignment())
                        <div class="col-md-3"><span class="fw-bold d-block">{{ __('stockexit.consignee') }}</span>{{ $stockExit->customer?->customer_name ?: '—' }}</div>
                    @endif
                </div>

                <div class="block w-full overflow-x-auto">
                    <table class="w-full mb-4 text-slate-900 border-collapse table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('product.code') }}</th>
                                <th>{{ __('product.name') }}</th>
                                <th class="text-end">{{ __('stockexit.quantity_out') }}</th>
                                <th class="text-end">{{ __('stockexit.quantity_returned') }}</th>
                                @if($stockExit->isConsignment())
                                    <th class="text-end">{{ __('stockexit.quantity_sold') }}</th>
                                @else
                                    <th class="text-end">{{ __('stockexit.quantity_lost') }}</th>
                                    <th class="text-end">{{ __('stockexit.quantity_outstanding') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockExit->details as $detail)
                                <tr>
                                    <td>{{ $detail->product->product_code }}</td>
                                    <td>{{ translatable_string($detail->product->product_name) }}</td>
                                    <td class="text-end">{{ $detail->quantity }}</td>
                                    <td class="text-end">{{ $detail->returned_quantity }}</td>
                                    @if($stockExit->isConsignment())
                                        <td class="text-end">{{ $detail->sold_quantity }}</td>
                                    @else
                                        <td class="text-end">{{ $detail->lost_quantity }}</td>
                                        <td class="text-end">{{ $detail->outstanding_quantity }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold border-t-2 border-slate-300">
                                <td colspan="2" class="text-end">{{ __('stockexit.total') }}</td>
                                <td class="text-end">{{ $stockExit->details->sum('quantity') }}</td>
                                <td class="text-end">{{ $stockExit->details->sum('returned_quantity') }}</td>
                                @if($stockExit->isConsignment())
                                    <td class="text-end">{{ $stockExit->details->sum('sold_quantity') }}</td>
                                @else
                                    <td class="text-end">{{ $stockExit->details->sum('lost_quantity') }}</td>
                                    <td class="text-end">{{ $stockExit->details->sum('outstanding_quantity') }}</td>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($stockExit->note)
                    <p class="mt-3"><span class="fw-bold">{{ __('stockexit.note') }}:</span> {{ $stockExit->note }}</p>
                @endif
            </div>
        </div>

        @if($stockExit->entries->isNotEmpty())
            <div class="card mt-4">
                <div class="flex-auto p-2">
                    <h5 class="mb-3">{{ __('stockexit.linked_entries') }}</h5>
                    <ul class="list-group">
                        @foreach($stockExit->entries as $entry)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('stock-entries.show', $entry->id) }}">{{ $entry->reference }}</a>
                                <span>
                                    @if($entry->sale)
                                        <a href="{{ route('sales.show', $entry->sale_id) }}" class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-info text-dark text-decoration-none me-2">
                                            <i class="bi bi-receipt"></i> {{ __('stockexit.generated_invoice') }}: {{ $entry->sale->reference }}
                                        </a>
                                    @endif
                                    {{ \Illuminate\Support\Carbon::parse($entry->date)->format('d M, Y') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
@endsection
