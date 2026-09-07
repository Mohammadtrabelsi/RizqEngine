@extends('layouts.app')

@section('title', __('sales.edit_payment'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('common.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">{{ __('sales.sales') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.show', $sale) }}">{{ $sale->reference }}</a></li>
        <li class="breadcrumb-item active">{{ __('sales.edit_payment') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form id="payment-form" action="{{ route('sale-payments.update', $salePayment) }}" method="POST">
            @csrf
            @method('patch')
            <div class="row">
                <div class="col-lg-12">
                    @include('utils.alerts')
                    <div class="mb-4">
                        <button class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">{{ __('sales.update_payment') }} <i class="bi bi-check"></i></button>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
                        <div class="flex-auto p-5">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="reference">{{ __('sales.reference') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="reference" required readonly value="{{ $salePayment->reference }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="date">{{ __('common.date') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="date" required value="{{ $salePayment->getAttributes()['date'] }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="due_amount">{{ __('sales.due_amount') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="due_amount" required value="{{ format_currency($sale->due_amount) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="amount">{{ __('sales.amount') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input id="amount" type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="amount" data-money-mask data-money-prefill required value="{{ old('amount') ?? $salePayment->amount }}">
                                            <div class="input-group-append">
                                                <button id="getTotalAmount" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700" type="button" data-money-fill="#amount" data-money-value="{{ $sale->due_amount }}">
                                                    <i class="bi bi-check-square"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="mb-4">
                                            <label for="payment_method">{{ __('sales.payment_method') }} <span class="text-danger">*</span></label>
                                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="payment_method" id="payment_method" required>
                                                <option {{ $salePayment->payment_method == 'Cash' ? 'selected' : '' }} value="Cash">{{ __('sales.cash') }}</option>
                                                <option {{ $salePayment->payment_method == 'Credit Card' ? 'selected' : '' }} value="Credit Card">{{ __('sales.credit_card') }}</option>
                                                <option {{ $salePayment->payment_method == 'Bank Transfer' ? 'selected' : '' }} value="Bank Transfer">{{ __('sales.bank_transfer') }}</option>
                                                <option {{ $salePayment->payment_method == 'Cheque' ? 'selected' : '' }} value="Cheque">{{ __('sales.cheque') }}</option>
                                                <option {{ $salePayment->payment_method == 'Other' ? 'selected' : '' }} value="Other">{{ __('sales.other') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="note">{{ __('sales.note') }}</label>
                                <textarea class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" rows="4" name="note">{{ old('note') ?? $salePayment->note }}</textarea>
                            </div>

                            <input type="hidden" value="{{ $sale->id }}" name="sale_id">
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

