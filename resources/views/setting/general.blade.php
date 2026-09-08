@extends('layouts.app')

@section('title', __('settings.settings'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('settings.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('settings.general_settings') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @include('utils.alerts')
                @include('setting._tabs')
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl bg-primary text-white">
                        <h5 class="mb-0">{{ __('settings.general_settings') }}</h5>
                    </div>
                    <div class="flex-auto p-2">
                        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('patch')
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="company_name">{{ __('settings.company_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="company_name" value="{{ $settings->company_name }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="company_email">{{ __('settings.company_email') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="company_email" value="{{ $settings->company_email }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="company_phone">{{ __('settings.company_phone') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="company_phone" value="{{ $settings->company_phone }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="client_name">Client Name</label>
                                        <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="client_name" value="{{ $settings->client_name }}" placeholder="Displayed in the navbars">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="client_logo">Client Logo</label>
                                        <input type="file" class="block w-full" name="client_logo" id="client_logo" accept="image/*">
                                        <small class="block mt-1 text-xs text-slate-500 text-muted">Displayed in the navbars. Max 2MB (jpeg, png, gif, svg, webp).</small>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label class="d-block">Current Logo</label>
                                        @if($settings->client_logo)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($settings->client_logo) }}" alt="Client Logo" class="logo-preview">
                                        @else
                                            <span class="text-muted">No logo uploaded</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="default_currency_id">Default Currency <span class="text-danger">*</span></label>
                                        <select name="default_currency_id" id="default_currency_id" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" required>
                                            @foreach(\App\Models\Currency::all() as $currency)
                                                <option {{ $settings->default_currency_id == $currency->id ? 'selected' : '' }} value="{{ $currency->id }}">{{ $currency->currency_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="default_currency_position">{{ __('settings.default_currency_position') }} <span class="text-danger">*</span></label>
                                        <select name="default_currency_position" id="default_currency_position" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" required>
                                            <option {{ $settings->default_currency_position == 'prefix' ? 'selected' : '' }} value="prefix">Prefix</option>
                                            <option {{ $settings->default_currency_position == 'suffix' ? 'selected' : '' }} value="suffix">Suffix</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-4">
                                        <label for="notification_email">{{ __('settings.notification_email') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="notification_email" value="{{ $settings->notification_email }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-12">
                                    <div class="mb-4">
                                        <label for="company_address">{{ __('settings.company_address') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="company_address" value="{{ $settings->company_address }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 mb-0">
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700"><i class="bi bi-check"></i> Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
