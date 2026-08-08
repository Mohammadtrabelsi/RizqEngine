<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>{{ __('password.reset') }} | {{ config('app.name') }}</title>

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
        <form class="login-form" method="post" action="{{ url('/password/email') }}">
            @csrf

            <div>
                <div class="login-title">{{ __('password.reset') }}</div>
                <div class="text-muted" style="font-size:14px">{{ __('password.reset-message') }}</div>
            </div>

            @if (session('status'))
                <div class="card elev-sm" style="border:1px solid var(--color-accent); color:var(--color-accent); padding:var(--space-3); font-size:13px">
                    {{ session('status') }}
                </div>
            @endif

            <div class="field">
                <label for="email">{{ __('password.email') }}</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email') }}" placeholder="you@store.com" autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn btn-primary btn-block" type="submit">{{ __('password.send-reset-link') }}</button>

            <div class="text-muted" style="font-size:13px; text-align:center">
                <a href="{{ route('login') }}">{{ __('login.sign-in') }}</a>
            </div>
        </form>
    </div>

</div>

<!-- CoreUI -->
@vite('resources/js/app.js')

</body>
</html>
