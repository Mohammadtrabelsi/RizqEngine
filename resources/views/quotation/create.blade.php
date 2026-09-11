@extends('layouts.app')

@section('title', __('quotations.create'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}"> {{ __('general.home') }} </a></li>
        <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}"> {{ __('quotations.quotations') }} </a></li>
        <li class="breadcrumb-item active"> {{ __('quotations.add') }} </li>
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
                @include('utils.alerts')
                <form id="quotation-form" action="{{ route('quotations.store') }}" method="POST">
                    @csrf

                    <div class="card mb-4">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                            <i class="bi bi-file-earmark-text text-primary"></i> {{ __('quotations.quotation_details') }}
                        </div>
                        <div class="flex-auto p-2">
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="reference">{{ __('quotations.reference') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference" required readonly value="QT">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="mb-4">
                                            <label for="customer_id">{{ __('quotations.customer') }} <span class="text-danger">*</span></label>
                                            <select class="form-control" name="customer_id" id="customer_id" required>
                                                @foreach(\App\Models\Customer::all() as $customer)
                                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="mb-4">
                                            <label for="date">{{ __('quotations.date') }}    <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="date" required value="{{ now()->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                            <i class="bi bi-cart text-primary"></i> {{ __('quotations.details') }}
                        </div>
                        <div class="flex-auto p-2">
                            <livewire:product-cart :cartInstance="'quotation'"/>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                            <i class="bi bi-info-circle text-primary"></i> {{ __('quotations.status') }} &amp; {{ __('quotations.note') }}
                        </div>
                        <div class="flex-auto p-2">
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="status">{{ __('quotations.status') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" name="status" id="status" required>
                                            <option value="Pending">{{ __('quotations.pending') }}</option>
                                            <option value="Sent">{{ __('quotations.sent') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label for="note">{{ __('quotations.note') }}</label>
                                <textarea name="note" id="note" rows="5" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            {{ __('quotations.create') }} <i class="bi bi-check"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')

@endpush
