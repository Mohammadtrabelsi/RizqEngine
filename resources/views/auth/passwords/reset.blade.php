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
    <!-- Application CSS (Tailwind) -->
    @vite('resources/css/app.css')
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>
<body>
<div class="login-grid">

    <div class="login-brand login-brand-copy">
        <a href="{{ route('welcome') }}" class="auth-brand-link">
            <x-logo :size="26" label="RizqEngine" />
        </a>

        <div class="auth-card">
            <div class="login-brand-lead text-white">{{ __('login.welcome') }}</div>
            <div class="auth-subtitle">{{ __('login.description') }}</div>
        </div>

        <div class="auth-fineprint text-muted">© {{ date('Y') }} RizqEngine</div>
    </div>

    <div class="login-form-wrap">
        <div class="login-form-card card">
            <div class="card-body">
                <form class="login-form" method="post" action="{{ url('/password/reset') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token ?? request()->route('token') }}">

                    <div class="login-form__heading">
                        <h2 class="login-form__title">{{ __('password.reset') }}</h2>
                        <p class="login-form__subtitle">{{ __('password.reset-message') }}</p>
                    </div>

                    <div class="login-form__field">
                        <label for="email">{{ __('password.email') }}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ $email ?? old('email') }}" placeholder="you@store.com" autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="login-form__field">
                        <label for="password">{{ __('password.password') }}</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                               name="password" placeholder="••••••••">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="login-form__field">
                        <label for="password_confirmation">{{ __('password.confirm-password') }}</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="form-control" placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">{{ __('password.reset') }}</button>
                </form>
            </div>
        </div>
    </div>

</div>

<!-- Application JS -->
@vite('resources/js/app.js')

</body>
</html>
