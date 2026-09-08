@extends('layouts.app')

@section('title', __('adjustment.adjustment_details'))

@push('page_css')
    @livewireStyles
@endpush

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('adjustments.index') }}">{{ __('adjustment.adjustments') }}</a></li>
        <li class="breadcrumb-item active">{{ __('adjustment.details') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="flex-auto p-2">
                        <ul class="list-group list-group-horizontal mb-4">
                            <li class="list-group-item flex-fill"><span class="fw-bold d-block">{{ __('adjustment.date') }}</span>{{ $adjustment->date }}</li>
                            <li class="list-group-item flex-fill"><span class="fw-bold d-block">{{ __('adjustment.reference') }}</span>{{ $adjustment->reference }}</li>
                        </ul>

                        <div class="row">
                            @foreach($adjustment->adjustedProducts as $adjustedProduct)
                                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                    <div class="card border h-100">
                                        <div class="flex-auto p-2">
                                            <h6 class="mb-1">{{ $adjustedProduct->product->product_name }}</h6>
                                            <p class="text-muted small mb-2">{{ $adjustedProduct->product->product_code }}</p>
                                            <ul class="list-group list-group-flush mb-0">
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>Quantity</span><span>{{ $adjustedProduct->quantity }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0">
                                                    <span>Type</span>
                                                    <span>
                                                        @if($adjustedProduct->type == 'add')
                                                            <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">(+) {{ __('adjustment.addition') }}</span>
                                                        @else
                                                            <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-red-100 text-red-700">(-) {{ __('adjustment.subtraction') }}</span>
                                                        @endif
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
