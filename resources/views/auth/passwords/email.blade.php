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
        <div class="login-form-card card">
            <div class="card-body">
                <form class="login-form" method="post" action="{{ url('/password/email') }}">
                    @csrf

                    <div class="login-form__heading">
                        <h2 class="login-form__title">{{ __('password.reset') }}</h2>
                        <p class="login-form__subtitle">{{ __('password.reset-message') }}</p>
                    </div>

                    @if (session('status'))
                        <div class="login-form__alert" style="border-color: var(--color-accent); color: var(--color-accent); background: rgba(145, 132, 217, 0.1);">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="login-form__field">
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
    </div>

</div>

<!-- CoreUI -->
@vite('resources/js/app.js')

</body>
</html>
