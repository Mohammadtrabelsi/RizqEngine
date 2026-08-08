<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>{{ __('login.sign-in') }} | {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('images/favicon.png') }}">
    <!-- CoreUI CSS (Nocturne theme) -->
    @vite('resources/css/app.css')
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>
<body>
<div class="login-grid">

    {{-- Brand panel --}}
    <div class="login-brand">
        <div class="login-brand__logo">
            <svg width="26" height="23" viewBox="0 0 100 90">
                <polygon points="50,5 27.5,47.5 72.5,47.5" fill="none" stroke="var(--color-text)" stroke-width="7"></polygon>
                <polygon points="5,90 27.5,47.5 50,90" fill="none" stroke="var(--color-text)" stroke-width="7"></polygon>
                <polygon points="95,90 72.5,47.5 50,90" fill="none" stroke="var(--color-text)" stroke-width="7"></polygon>
                <polygon points="27.5,47.5 72.5,47.5 50,90" fill="var(--color-accent)" stroke="var(--color-accent)" stroke-width="7"></polygon>
            </svg>
            <span class="login-brand__name">Triangle POS</span>
        </div>

        <div class="login-brand__copy">
            <h1 class="login-brand__title">{{ __('login.welcome') }}</h1>
            <p class="login-brand__text">{{ __('login.description') }}</p>
        </div>

        <div class="login-brand__footer">© {{ date('Y') }} Triangle POS</div>
    </div>

    {{-- Form panel --}}
    <div class="login-form-wrap">
        <div class="login-form-card card">
            <div class="card-body">
                <div class="login-lang" style="display:flex; justify-content:flex-end; margin-bottom:1rem;">
                    @include('includes.language-switcher')
                </div>
                <form id="login" class="login-form" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="login-form__heading">
                        <h2 class="login-form__title">{{ __('login.sign-in') }}</h2>
                        <p class="login-form__subtitle">{{ __('login.welcome-back') }}</p>
                    </div>

                    @if(Session::has('account_deactivated'))
                        <div class="login-form__alert">
                            {{ Session::get('account_deactivated') }}
                        </div>
                    @endif

                    <div class="login-form__field">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                            </div>
                            <input id="email" type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}"
                                   placeholder="{{ __('login.email') }}" autocomplete="email" autofocus>
                        </div>
                        @error('email')
                            <div class="login-form__error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="login-form__field">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            </div>
                            <input id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password"
                                   placeholder="{{ __('login.password') }}" autocomplete="current-password">
                        </div>
                        @error('password')
                            <div class="login-form__error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="login-form__actions">
                        <button id="submit" class="btn btn-primary login-form__submit" type="submit">
                            {{ __('login.sign-in') }}
                            <span id="spinner" class="spinner-border text-info login-spinner" role="status">
                                <span class="sr-only">Loading...</span>
                            </span>
                        </button>
                        <a class="btn btn-link login-form__forgot" href="{{ route('password.request') }}">
                            {{ __('login.forgot-password') }}
                        </a>
                    </div>

                    <div class="login-form__contact">
                        {{ __('login.contact-owner-message') }}
                        <a href="{{ route('welcome') }}">{{ __('login.contact-owner') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<!-- CoreUI -->
@vite(['resources/js/app.js', 'resources/js/login.js'])

</body>
</html>
