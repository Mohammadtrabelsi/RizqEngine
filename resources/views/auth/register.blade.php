<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>{{ __('auth.register') }} | {{ config('app.name') }}</title>

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
        <div class="login-form-card relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
            <div class="flex-auto p-5">
                <form class="login-form" method="post" action="{{ url('/register') }}">
                    @csrf

                    <div class="login-form__heading">
                        <h2 class="login-form__title">{{ __('auth.register') }}</h2>
                        <p class="login-form__subtitle">{{ __('auth.create-account') }}</p>
                    </div>

                    <div class="login-form__field">
                        <label for="name">{{ __('auth.full-name') }}</label>
                        <input id="name" type="text" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('name') !border-red-500 @enderror"
                               name="name" value="{{ old('name') }}" placeholder="{{ __('auth.full-name') }}" autofocus>
                        @error('name')
                            <div class="block w-full mt-1 text-xs text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="login-form__field">
                        <label for="email">{{ __('auth.email') }}</label>
                        <input id="email" type="email" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('email') !border-red-500 @enderror"
                               name="email" value="{{ old('email') }}" placeholder="you@store.com">
                        @error('email')
                            <div class="block w-full mt-1 text-xs text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="login-form__field">
                        <label for="password">{{ __('login.password') }}</label>
                        <input id="password" type="password" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('password') !border-red-500 @enderror"
                               name="password" placeholder="••••••••">
                        @error('password')
                            <div class="block w-full mt-1 text-xs text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="login-form__field">
                        <label for="password_confirmation">{{ __('auth.confirm-password') }}</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="••••••••">
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700 flex w-full">{{ __('auth.register') }}</button>

                    <div class="auth-footnote text-muted">
                        <a href="{{ route('login') }}">{{ __('auth.already-have-account') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<!-- Application JS -->
@vite('resources/js/app.js')

</body>
</html>
