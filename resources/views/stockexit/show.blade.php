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
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
                    <h4 class="mb-0">{{ __('stockexit.bon_de_sortie') }} — {{ $stockExit->reference }}</h4>
                    <div>
                        @if($stockExit->status === \App\Models\StockExit::STATUS_CLOSED)
                            <span class="badge bg-success p-2">{{ __('stockexit.status_closed') }}</span>
                        @else
                            <span class="badge bg-warning text-dark p-2">{{ __('stockexit.status_in_transit') }}</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3"><span class="fw-bold d-block">{{ __('stockexit.date') }}</span>{{ \Illuminate\Support\Carbon::parse($stockExit->date)->format('d M, Y') }}</div>
                    <div class="col-md-3"><span class="fw-bold d-block">{{ __('stockexit.reason') }}</span>{{ $stockExit->reason ?: '—' }}</div>
                    <div class="col-md-3"><span class="fw-bold d-block">{{ __('stockexit.destination') }}</span>{{ $stockExit->destination ?: '—' }}</div>
                    <div class="col-md-3"><span class="fw-bold d-block">{{ __('stockexit.responsible') }}</span>{{ $stockExit->responsible ?: '—' }}</div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('product.code') }}</th>
                                <th>{{ __('product.name') }}</th>
                                <th class="text-end">{{ __('stockexit.quantity_out') }}</th>
                                <th class="text-end">{{ __('stockexit.quantity_returned') }}</th>
                                <th class="text-end">{{ __('stockexit.quantity_lost') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockExit->details as $detail)
                                <tr>
                                    <td>{{ $detail->product->product_code }}</td>
                                    <td>{{ translatable_string($detail->product->product_name) }}</td>
                                    <td class="text-end">{{ $detail->quantity }}</td>
                                    <td class="text-end">{{ $detail->returned_quantity }}</td>
                                    <td class="text-end">{{ $detail->lost_quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($stockExit->note)
                    <p class="mt-3"><span class="fw-bold">{{ __('stockexit.note') }}:</span> {{ $stockExit->note }}</p>
                @endif

                <div class="mt-3">
                    @can('create_stock_entries')
                        @if($stockExit->status !== \App\Models\StockExit::STATUS_CLOSED)
                            <a href="{{ route('stock-entries.create', $stockExit->id) }}" class="btn btn-success">
                                <i class="bi bi-box-arrow-in-down"></i> {{ __('stockexit.declare_return') }}
                            </a>
                        @endif
                    @endcan
                    <a href="{{ route('stock-exits.index') }}" class="btn btn-secondary">{{ __('app.back') }}</a>
                </div>
            </div>
        </div>

        @if($stockExit->entries->isNotEmpty())
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('stockexit.linked_entries') }}</h5>
                    <ul class="list-group">
                        @foreach($stockExit->entries as $entry)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('stock-entries.show', $entry->id) }}">{{ $entry->reference }}</a>
                                <span>{{ \Illuminate\Support\Carbon::parse($entry->date)->format('d M, Y') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
@endsection
