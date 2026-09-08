@extends('layouts.app')

@section('title', __('purchase-returns.create'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('purchase-returns.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchase-returns.index') }}">{{ __('purchase-returns.purchase_returns') }}</a></li>
        <li class="breadcrumb-item active">{{ __('purchase-returns.add') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-12">
                <livewire:search-product/>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
                    <div class="flex-auto p-2">
                        @include('utils.alerts')
                        <form id="purchase-return-form" action="{{ route('purchase-returns.store') }}" method="POST">
                            @csrf
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="reference">{{ __('purchase-returns.reference') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="reference" required readonly value="PRRN">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="mb-4">
                                            <label for="supplier_id">{{ __('purchase-returns.supplier') }} <span class="text-danger">*</span></label>
                                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="supplier_id" id="supplier_id" required>
                                                @foreach(\App\Models\Supplier::all() as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="mb-4">
                                            <label for="date">{{ __('purchase-returns.date') }} <span class="text-danger">*</span></label>
                                            <input type="date" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="date" required value="{{ now()->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <livewire:product-cart :cartInstance="'purchase_return'"/>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="status">{{ __('purchase-returns.status') }} <span class="text-danger">*</span></label>
                                        <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="status" id="status" required>
                                            <option value="Pending">{{ __('purchase-returns.pending') }}</option>
                                            <option value="Shipped">{{ __('purchase-returns.shipped') }}</option>
                                            <option value="Completed">{{ __('purchase-returns.completed') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="mb-4">
                                            <label for="payment_method">{{ __('purchase-returns.payment_method') }} <span class="text-danger">*</span></label>
                                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="payment_method" id="payment_method" required>
                                                <option value="Cash">{{ __('purchase-returns.cash') }}</option>
                                                <option value="Credit Card">{{ __('purchase-returns.credit_card') }}</option>
                                                <option value="Bank Transfer">{{ __('purchase-returns.bank_transfer') }}</option>
                                                <option value="Cheque">{{ __('purchase-returns.cheque') }}</option>
                                                <option value="Other">{{ __('purchase-returns.other') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="paid_amount">{{ __('purchase-returns.paid_amount') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input id="paid_amount" type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="paid_amount" data-money-mask data-money-allow-zero required>
                                            <div class="input-group-append">
                                                <button id="getTotalAmount" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700" type="button" data-money-fill="#paid_amount" data-money-value="{{ Cart::instance('purchase_return')->total() }}">
                                                    <i class="bi bi-check-square"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="note">{{ __('purchase-returns.note') }}</label>
                                <textarea name="note" id="note" rows="5" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45"></textarea>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                                    {{ __('purchase-returns.create_return') }} <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    @include('includes.money-mask-js')
@endpush
