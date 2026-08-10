@extends('layouts.app')

@section('title', __('stockexit.entry_details'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('stock-exits.index') }}">{{ __('stockexit.stock_exits') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('stock-exits.show', $stockEntry->stock_exit_id) }}">{{ $stockEntry->stockExit->reference }}</a></li>
        <li class="breadcrumb-item active">{{ $stockEntry->reference }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-1">{{ __('stockexit.bon_dentree') }} — {{ $stockEntry->reference }}</h4>
                <ul class="list-group list-group-horizontal mb-4 flex-wrap">
                    <li class="list-group-item flex-fill"><span class="fw-bold d-block">{{ __('stockexit.origin_reference') }}</span>{{ $stockEntry->stockExit->reference }}</li>
                    <li class="list-group-item flex-fill"><span class="fw-bold d-block">{{ __('stockexit.reception_date') }}</span>{{ \Illuminate\Support\Carbon::parse($stockEntry->date)->format('d M, Y') }}</li>
                </ul>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('product.code') }}</th>
                                <th>{{ __('product.name') }}</th>
                                <th class="text-end">{{ __('stockexit.quantity_out') }}</th>
                                <th class="text-end">{{ __('stockexit.quantity_received') }}</th>
                                <th class="text-end">{{ __('stockexit.quantity_lost') }}</th>
                                <th>{{ __('stockexit.line_status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockEntry->details as $detail)
                                <tr>
                                    <td>{{ $detail->product->product_code }}</td>
                                    <td>{{ translatable_string($detail->product->product_name) }}</td>
                                    <td class="text-end">{{ $detail->quantity_out }}</td>
                                    <td class="text-end">{{ $detail->quantity_returned }}</td>
                                    <td class="text-end">{{ $detail->quantity_lost }}</td>
                                    <td>
                                        @if($detail->quantity_lost === 0)
                                            <span class="badge bg-success">{{ __('stockexit.full_return') }}</span>
                                        @elseif($detail->quantity_returned === 0)
                                            <span class="badge bg-danger">{{ __('stockexit.fully_consumed') }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ __('stockexit.partial_return') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($stockEntry->note)
                    <p class="mt-3"><span class="fw-bold">{{ __('stockexit.note') }}:</span> {{ $stockEntry->note }}</p>
                @endif

                <a href="{{ route('stock-exits.show', $stockEntry->stock_exit_id) }}" class="btn btn-secondary mt-2">{{ __('app.back') }}</a>
            </div>
        </div>
    </div>
@endsection
