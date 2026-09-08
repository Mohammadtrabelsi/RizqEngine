@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 verify-wrapper">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 verify-card">
                    <div class="flex-auto p-2">
                        <h4 class="mb-3 text-lg font-semibold text-slate-900">{{ __('auth.verify-email') }}</h4>
                        @if (session('resent'))
                            <p class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-emerald-50 text-emerald-700 border-emerald-200" role="alert">
                                {{ __('auth.verification-link-sent') }}
                            </p>
                        @endif
                        <p class="mb-0">{{ __('auth.before-proceeding') }} {{ __('auth.check-your-email') }}.</p>
                        <a href="{{ route('verification.resend') }}">{{ __('auth.request-another') }}</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection