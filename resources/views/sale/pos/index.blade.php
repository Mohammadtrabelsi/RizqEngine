@extends('layouts.app')

@section('title', __('sales.pos'))

@section('third_party_stylesheets')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('sales.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('sales.pos') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @include('utils.alerts')
            </div>
            <div class="col-12 col-lg-8 mb-4">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                        <h5 class="mb-0"><i class="bi bi-search text-primary"></i> {{ __('general.search-product') }}</h5>
                    </div>
                    <div class="flex-auto p-2">
                        <livewire:search-product/>
                        <hr class="my-3">
                        <livewire:pos.filter :categories="$product_categories"/>
                    </div>
                </div>
                <livewire:pos.product-list :categories="$product_categories"/>
            </div>
            <div class="col-12 col-lg-4 mb-4">
                <div class="pos-checkout-sticky">
                    <livewire:pos.checkout :cart-instance="'sale'" :customers="$customers"/>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    @include('includes.money-mask-js')
    @vite('resources/js/pos-checkout.js')
@endpush
