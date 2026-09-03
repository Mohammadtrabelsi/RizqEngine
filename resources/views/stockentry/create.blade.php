@extends('layouts.app')

@section('title', __('stockexit.declare_return'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('stock-exits.index') }}">{{ __('stockexit.stock_exits') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('stock-exits.show', $stockExit->id) }}">{{ $stockExit->reference }}</a></li>
        <li class="breadcrumb-item active">{{ __('stockexit.declare_return') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        @include('utils.alerts')
        <div class="card">
            <div class="card-body">
                <h4 class="mb-1">{{ __('stockexit.bon_dentree') }}</h4>
                <p class="text-muted">{{ __('stockexit.origin_reference') }}: <strong>{{ $stockExit->reference }}</strong></p>
                @if($stockExit->isConsignment() && $stockExit->customer)
                    <p class="text-muted">{{ __('stockexit.consignee') }}: <strong>{{ $stockExit->customer->customer_name }}</strong></p>
                @endif

                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    {{ $stockExit->isConsignment() ? __('stockexit.consignment_return_hint') : __('stockexit.return_control_hint') }}
                </div>

                <form action="{{ route('stock-entries.store', $stockExit->id) }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="date">{{ __('stockexit.reception_date') }} <span class="text-danger">*</span></label>
                                <input type="date" id="date" name="date" class="form-control" required value="{{ old('date', now()->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('product.code') }}</th>
                                    <th>{{ __('product.name') }}</th>
                                    <th class="text-end">{{ __('stockexit.quantity_out') }}</th>
                                    <th class="text-end">{{ __('stockexit.quantity_outstanding') }}</th>
                                    <th class="img-w-180">{{ __('stockexit.quantity_received') }}</th>
                                    <th class="img-w-180">{{ __('stockexit.quantity_lost') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockExit->details as $detail)
                                    @continue($detail->outstanding_quantity <= 0)
                                    <tr>
                                        <td>{{ $detail->product->product_code }}</td>
                                        <td>{{ translatable_string($detail->product->product_name) }}</td>
                                        <td class="text-end">{{ $detail->quantity }}</td>
                                        <td class="text-end">{{ $detail->outstanding_quantity }}</td>
                                        <td>
                                            <input type="hidden" name="detail_ids[]" value="{{ $detail->id }}">
                                            <input type="number" name="returned[]" class="form-control"
                                                   min="0" max="{{ $detail->outstanding_quantity }}"
                                                   value="{{ $detail->outstanding_quantity }}" required>
                                        </td>
                                        <td>
                                            <input type="number" name="lost[]" class="form-control"
                                                   min="0" max="{{ $detail->outstanding_quantity }}"
                                                   value="0">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group">
                        <label for="note">{{ __('stockexit.note') }}</label>
                        <textarea name="note" id="note" rows="3" class="form-control">{{ old('note') }}</textarea>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">
                            {{ __('stockexit.confirm_return') }} <i class="bi bi-check"></i>
                        </button>
                        <a href="{{ route('stock-exits.show', $stockExit->id) }}" class="btn btn-secondary">{{ __('app.back') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
