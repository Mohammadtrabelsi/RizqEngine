@extends('layouts.app')

@section('title', __('settings.tax_settings'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('settings.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('settings.tax_settings') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @include('utils.alerts')
                @include('setting._tabs')
                <div class="card">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl bg-primary text-white">
                        <h5 class="mb-0">{{ __('settings.tax_settings') }}</h5>
                    </div>
                    <div class="flex-auto p-2">
                        <form action="{{ route('settings.tax.update') }}" method="POST">
                            @csrf
                            @method('patch')

                            <p class="mb-4 text-sm text-slate-500 text-muted">{{ __('settings.tax_settings_hint') }}</p>

                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="purchase_tax_mode">{{ __('settings.purchase_tax_mode') }} <span class="text-danger">*</span></label>
                                        <select name="purchase_tax_mode" id="purchase_tax_mode" class="form-control" required>
                                            <option {{ $settings->purchase_tax_mode === 'included' ? 'selected' : '' }} value="included">{{ __('taxes.tax_included') }}</option>
                                            <option {{ $settings->purchase_tax_mode === 'excluded' ? 'selected' : '' }} value="excluded">{{ __('taxes.tax_excluded') }}</option>
                                        </select>
                                        <small class="block mt-1 text-xs text-slate-500 text-muted">{{ __('settings.purchase_tax_mode_hint') }}</small>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="sale_tax_mode">{{ __('settings.sale_tax_mode') }} <span class="text-danger">*</span></label>
                                        <select name="sale_tax_mode" id="sale_tax_mode" class="form-control" required>
                                            <option {{ $settings->sale_tax_mode === 'included' ? 'selected' : '' }} value="included">{{ __('taxes.tax_included') }}</option>
                                            <option {{ $settings->sale_tax_mode === 'excluded' ? 'selected' : '' }} value="excluded">{{ __('taxes.tax_excluded') }}</option>
                                        </select>
                                        <small class="block mt-1 text-xs text-slate-500 text-muted">{{ __('settings.sale_tax_mode_hint') }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 mb-0">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check"></i> {{ __('settings.save_changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
