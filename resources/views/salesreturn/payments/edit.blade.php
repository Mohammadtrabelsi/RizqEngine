@extends('layouts.app')

@section('title', __('sales.edit_payment'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('sales.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sale-returns.index') }}">{{ __('sales.sale_returns') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sale-returns.show', $sale_return) }}">{{ $sale_return->reference }}</a></li>
        <li class="breadcrumb-item active">{{ __('sales.edit_payment') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form id="payment-form" action="{{ route('sale-return-payments.update', $saleReturnPayment) }}" method="POST">
            @csrf
            @method('patch')
            <div class="row">
                <div class="col-lg-12">
                    @include('utils.alerts')
                    <div class="form-group">
                        <button class="btn btn-primary">{{ __('sales.update_payment') }} <i class="bi bi-check"></i></button>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="reference">{{ __('sales.reference') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference" required readonly value="{{ $saleReturnPayment->reference }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="date">{{ __('sales.date') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" required value="{{ $saleReturnPayment->getAttributes()['date'] }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="due_amount">{{ __('sales.due_amount') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="due_amount" required value="{{ format_currency($sale_return->due_amount) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="amount">{{ __('sales.amount') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input id="amount" type="text" class="form-control" name="amount" data-money-mask data-money-prefill required value="{{ old('amount') ?? $saleReturnPayment->amount }}">
                                            <div class="input-group-append">
                                                <button id="getTotalAmount" class="btn btn-primary" type="button" data-money-fill="#amount" data-money-value="{{ $sale_return->due_amount }}">
                                                    <i class="bi bi-check-square"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="form-group">
                                            <label for="payment_method">{{ __('sales.payment_method') }} <span class="text-danger">*</span></label>
                                            <select class="form-control" name="payment_method" id="payment_method" required>
                                                <option {{ $saleReturnPayment->payment_method == 'Cash' ? 'selected' : '' }} value="Cash">{{ __('sales.cash') }}</option>
                                                <option {{ $saleReturnPayment->payment_method == 'Credit Card' ? 'selected' : '' }} value="Credit Card">{{ __('sales.credit_card') }}</option>
                                                <option {{ $saleReturnPayment->payment_method == 'Bank Transfer' ? 'selected' : '' }} value="Bank Transfer">{{ __('sales.bank_transfer') }}</option>
                                                <option {{ $saleReturnPayment->payment_method == 'Cheque' ? 'selected' : '' }} value="Cheque">{{ __('sales.cheque') }}</option>
                                                <option {{ $saleReturnPayment->payment_method == 'Other' ? 'selected' : '' }} value="Other">{{ __('sales.other') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="note">{{ __('sales.note') }}</label>
                                <textarea class="form-control" rows="4" name="note">{{ old('note') ?? $saleReturnPayment->note }}</textarea>
                            </div>

                            <input type="hidden" value="{{ $sale_return->id }}" name="sale_return_id">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('page_scripts')
    @include('includes.money-mask-js')
@endpush

