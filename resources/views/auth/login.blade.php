<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>{{ __('login.sign-in') }} | {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('images/favicon.png') }}">
    <!-- CoreUI CSS (Nocturne theme) -->
    @vite('resources/sass/app.scss')
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>
<body class="c-dark-theme">
<div class="login-grid">

    <div class="login-brand login-brand-copy">
        <a href="{{ route('welcome') }}" style="text-decoration:none; color:var(--color-text);">
            @include('layouts.logo', ['size' => 26, 'label' => 'Triangle POS', 'labelSize' => 15])
        </a>

        <div style="max-width:420px">
            <div class="login-brand-lead">{{ __('login.welcome') }}</div>
            <div style="font-size:15px; line-height:1.6; color:var(--color-neutral-300)">{{ __('login.description') }}</div>
        </div>

        <div class="text-muted" style="font-size:12px">© {{ date('Y') }} Triangle POS</div>
    </div>

    <div class="login-form-wrap">
        <form id="login" class="login-form" method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <div class="login-title">{{ __('login.sign-in') }}</div>
                <div class="text-muted" style="font-size:14px">{{ __('login.welcome-back') }}</div>
            </div>

            @if(Session::has('account_deactivated'))
                <div class="card elev-sm" style="border:1px solid var(--color-accent); color:#e0645f; padding:var(--space-3); font-size:13px">
                    {{ Session::get('account_deactivated') }}
                </div>
            @endif

            <div class="field">
                <label for="email">{{ __('login.email') }}</label>
                <input id="email" type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email') }}"
                       placeholder="you@store.com" autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">{{ __('login.password') }}</label>
                <input id="password" type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password" placeholder="••••••••">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex align-items-center justify-content-between" style="font-size:13px">
                <label class="d-inline-flex align-items-center mb-0" style="gap:6px; cursor:pointer">
                    <input type="checkbox" name="remember"> {{ __('login.remember-me') }}
                </label>
                <a href="{{ route('password.request') }}">{{ __('login.forgot-password') }}</a>
            </div>

            <button id="submit" class="btn btn-primary btn-block d-flex align-items-center justify-content-center" type="submit">
                {{ __('login.sign-in') }}
                <div id="spinner" class="spinner-border login-spinner" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </button>

            <div class="text-muted" style="font-size:13px; text-align:center">
                {{ __('login.contact-owner-message') }} <a href="{{ route('welcome') }}">{{ __('login.contact-owner') }}</a>
            </div>
        </form>
    </div>

</div>

<!-- CoreUI -->
@vite(['resources/js/app.js', 'resources/js/login.js'])

</body>
</html>
