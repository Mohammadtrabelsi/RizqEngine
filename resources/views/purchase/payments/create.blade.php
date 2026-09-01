@extends('layouts.app')

@section('title', __('purchase.add_payment'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('purchase.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">{{ __('purchase.purchases') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchases.show', $purchase) }}">{{ $purchase->reference }}</a></li>
        <li class="breadcrumb-item active">{{ __('purchase.add_payment') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form id="payment-form" action="{{ route('purchase-payments.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    @include('utils.alerts')
                    <div class="form-group">
                        <button class="btn btn-primary">{{ __('purchase.create_payment') }} <i class="bi bi-check"></i></button>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="reference">{{ __('purchase.reference') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference" required readonly value="INV/{{ $purchase->reference }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="date">{{ __('purchase.date') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" required value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="due_amount">{{ __('purchase.due_amount') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="due_amount" required value="{{ format_currency($purchase->due_amount) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="amount">{{ __('purchase.amount') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input id="amount" type="text" class="form-control" name="amount" data-money-mask required value="{{ old('amount') }}">
                                            <div class="input-group-append">
                                                <button id="getTotalAmount" class="btn btn-primary" type="button" data-money-fill="#amount" data-money-value="{{ $purchase->due_amount }}">
                                                    <i class="bi bi-check-square"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="form-group">
                                            <label for="payment_method">{{ __('purchase.payment_method') }} <span class="text-danger">*</span></label>
                                            <select class="form-control" name="payment_method" id="payment_method" required>
                                                <option value="Cash">{{ __('purchase.cash') }}</option>
                                                <option value="Credit Card">{{ __('purchase.credit_card') }}</option>
                                                <option value="Bank Transfer">{{ __('purchase.bank_transfer') }}</option>
                                                <option value="Cheque">{{ __('purchase.cheque') }}</option>
                                                <option value="Other">{{ __('purchase.other') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="note">{{ __('purchase.note') }}</label>
                                <textarea class="form-control" rows="4" name="note">{{ old('note') }}</textarea>
                            </div>

                            <input type="hidden" value="{{ $purchase->id }}" name="purchase_id">
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

