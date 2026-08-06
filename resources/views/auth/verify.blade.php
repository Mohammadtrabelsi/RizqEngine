@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8" style="margin-top: 2%">
                <div class="card" style="width: 40rem;">
                    <div class="card-body">
                        <h4 class="card-title">{{ __('auth.verify-email') }}</h4>
                        @if (session('resent'))
                            <p class="alert alert-success" role="alert">
                                {{ __('auth.verification-link-sent') }}
                            </p>
                        @endif
                        <p class="card-text">{{ __('auth.before-proceeding') }} {{ __('auth.check-your-email') }}.</p>
                        <a href="{{ route('verification.resend') }}">{{ __('auth.request-another') }}</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection