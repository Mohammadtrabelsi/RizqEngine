@extends('layouts.app')

@section('title', __('purchase.add_purchase'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">{{ __('purchase.purchases') }}</a></li>
        <li class="breadcrumb-item active">{{ __('purchase.add_purchase') }}</li>
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
                        <form id="purchase-form" action="{{ route('purchases.store') }}" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="reference">{{ __('purchase.reference') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="reference" required readonly value="PR">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="mb-4">
                                            <label for="supplier_id">{{ __('purchase.supplier') }} <span class="text-danger">*</span></label>
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
                                            <label for="date">{{ __('purchase.date') }} <span class="text-danger">*</span></label>
                                            <input type="date" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="date" required value="{{ now()->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="warehouse_id">{{ __('warehouses.warehouse') }}</label>
                                        <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="warehouse_id" id="warehouse_id">
                                            @foreach(\App\Models\Warehouse::active()->orderByDesc('is_default')->get() as $warehouse)
                                                <option value="{{ $warehouse->id }}" {{ $warehouse->is_default ? 'selected' : '' }}>{{ $warehouse->name }}{{ $warehouse->is_default ? ' ('.__('warehouses.default').')' : '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <livewire:product-cart :cartInstance="'purchase'"/>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="status">{{ __('purchase.status') }} <span class="text-danger">*</span></label>
                                        <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="status" id="status" required>
                                            <option value="Pending">{{ __('purchase.pending') }}</option>
                                            <option value="Ordered">{{ __('purchase.ordered') }}</option>
                                            <option value="Completed">{{ __('purchase.completed') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="mb-4">
                                            <label for="payment_method">{{ __('purchase.payment_method') }} <span class="text-danger">*</span></label>
                                            <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="payment_method" id="payment_method" required>
                                                <option value="Cash">{{ __('purchase.cash') }}</option>
                                                <option value="Credit Card">{{ __('purchase.credit_card') }}</option>
                                                <option value="Bank Transfer">{{ __('purchase.bank_transfer') }}</option>
                                                <option value="Cheque">{{ __('purchase.cheque') }}</option>
                                                <option value="Other">{{ __('purchase.other') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="paid_amount">{{ __('purchase.paid_amount') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input id="paid_amount" type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="paid_amount" data-money-mask data-money-allow-zero required>
                                            <div class="input-group-append">
                                                <button id="getTotalAmount" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700" type="button" data-money-fill="#paid_amount" data-money-value="{{ Cart::instance('purchase')->total() }}">
                                                    <i class="bi bi-check-square"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="note">{{ __('purchase.note') }} ({{ __('purchase.optional') }})</label>
                                <textarea name="note" id="note" rows="5" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45"></textarea>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                                    {{ __('purchase.create_purchase') }} <i class="bi bi-check"></i>
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
