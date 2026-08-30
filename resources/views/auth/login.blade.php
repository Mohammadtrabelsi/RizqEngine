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
    <!-- Application CSS -->
    @vite('resources/css/app.css')
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>
<body class="bg-slate-50 text-slate-900">

<div class="login-grid">

    {{-- Brand panel --}}
    <div class="login-brand" style="background: linear-gradient(135deg, #eef2ff 0%, #f8fafc 100%); border-right: 1px solid #e2e8f0;">
        <div class="login-brand__logo d-flex align-items-center gap-2">
            <svg width="26" height="23" viewBox="0 0 100 90" aria-hidden="true">
                <polygon points="50,5 27.5,47.5 72.5,47.5" fill="none" stroke="#0f172a" stroke-width="7" stroke-linejoin="round"></polygon>
                <polygon points="5,90 27.5,47.5 50,90" fill="none" stroke="#0f172a" stroke-width="7" stroke-linejoin="round"></polygon>
                <polygon points="95,90 72.5,47.5 50,90" fill="none" stroke="#0f172a" stroke-width="7" stroke-linejoin="round"></polygon>
                <polygon points="27.5,47.5 72.5,47.5 50,90" fill="#4f46e5" stroke="#4f46e5" stroke-width="7" stroke-linejoin="round"></polygon>
            </svg>
            <span class="login-brand__name text-slate-900 font-weight-bold">Triangle POS</span>
        </div>

        <div class="login-brand__copy">
            <h1 class="login-brand__title text-slate-900 font-weight-bold">{{ __('login.welcome') }}</h1>
            <p class="login-brand__text text-slate-600 mb-0">{{ __('login.description') }}</p>
        </div>

        <div class="login-brand__footer text-slate-500 small">© {{ date('Y') }} Triangle POS</div>
    </div>

    {{-- Form panel --}}
    <div class="login-form-wrap bg-slate-50">
        <div class="login-form-card card bg-white border rounded-lg shadow-md">
            <div class="card-body p-4 p-md-5">
                <div class="login-lang d-flex justify-content-end mb-4">
                    @include('includes.language-switcher')
                </div>

                <form id="login" class="login-form d-flex flex-column gap-3" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="login-form__heading mb-2">
                        <h2 class="login-form__title h3 font-weight-bold text-slate-900 mb-1">{{ __('login.sign-in') }}</h2>
                        <p class="login-form__subtitle text-slate-500 small mb-0">{{ __('login.welcome-back') }}</p>
                    </div>

                    @if(Session::has('account_deactivated'))
                        <div class="alert alert-warning py-2 px-3 small">
                            {{ Session::get('account_deactivated') }}
                        </div>
                    @endif

                    <div class="field form-group mb-3">
                        <label for="email" class="form-label text-slate-700 small mb-1">{{ __('login.email') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-slate-50 border-slate-300"><i class="bi bi-envelope text-indigo-600"></i></span>
                            <input id="email" type="email"
                                   class="form-control input @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}"
                                   placeholder="you@store.com" autocomplete="email" autofocus>
                        </div>
                        @error('email')
                            <div class="login-form__error text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field form-group mb-3">
                        <label for="password" class="form-label text-slate-700 small mb-1">{{ __('login.password') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-slate-50 border-slate-300"><i class="bi bi-lock text-indigo-600"></i></span>
                            <input id="password" type="password"
                                   class="form-control input @error('password') is-invalid @enderror"
                                   name="password"
                                   placeholder="••••••••" autocomplete="current-password">
                        </div>
                        @error('password')
                            <div class="login-form__error text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex align-items-center justify-content-between my-2">
                        <div class="form-check custom-control custom-checkbox mb-0">
                            <input class="custom-control-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="custom-control-label text-slate-600 small" for="remember">
                                Remember me
                            </label>
                        </div>
                        <a class="text-indigo-600 small text-decoration-none font-weight-bold" href="{{ route('password.request') }}">
                            {{ __('login.forgot-password') }}
                        </a>
                    </div>

                    <div class="login-form__actions mt-3">
                        <button id="submit" class="btn btn-primary btn-block py-2 shadow-sm" type="submit">
                            <span>{{ __('login.sign-in') }}</span>
                            <span id="spinner" class="spinner-border spinner-border-sm text-light login-spinner ms-2" role="status">
                                <span class="sr-only">Loading...</span>
                            </span>
                        </button>
                    </div>

                    <div class="login-form__contact text-center text-slate-500 small mt-4">
                        {{ __('login.contact-owner-message') }}
                        <a href="{{ route('welcome') }}" class="text-indigo-600 font-weight-bold ms-1">{{ __('login.contact-owner') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<!-- Application JS -->
@vite(['resources/js/app.js', 'resources/js/login.js'])

</body>
</html>
