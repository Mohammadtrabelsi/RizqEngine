<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>Sign in | {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <!-- CoreUI CSS -->
    @vite('resources/sass/app.scss')
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>
<body>
<div class="login-grid">

    <div class="login-brand">
        <div style="display:flex; align-items:center; gap:var(--space-3)">
            <svg width="26" height="23" viewBox="0 0 100 90">
                <polygon points="50,5 27.5,47.5 72.5,47.5" fill="none" stroke="var(--color-text)" stroke-width="7"></polygon>
                <polygon points="5,90 27.5,47.5 50,90" fill="none" stroke="var(--color-text)" stroke-width="7"></polygon>
                <polygon points="95,90 72.5,47.5 50,90" fill="none" stroke="var(--color-text)" stroke-width="7"></polygon>
                <polygon points="27.5,47.5 72.5,47.5 50,90" fill="var(--color-accent)" stroke="var(--color-accent)" stroke-width="7"></polygon>
            </svg>
            <div style="font-family:var(--font-heading); font-weight:500; font-size:15px">Triangle POS</div>
        </div>

        <div style="max-width:420px">
            <div style="font-family:var(--font-heading); font-weight:500; font-size:32px; line-height:1.2; margin-bottom:var(--space-4)">{{ __('login.welcome') }}</div>
            <div style="font-size:15px; line-height:1.6; color:var(--color-neutral-300)">{{ __('login.description') }}</div>
        </div>

        <div class="text-muted" style="font-size:12px">© {{ date('Y') }} Triangle POS</div>
    </div>

    <div class="login-form-wrap">
        <form id="login" class="login-form" method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <div style="font-family:var(--font-heading); font-weight:500; font-size:24px; margin-bottom:var(--space-2)">{{ __('login.sign-in') }}</div>
                <div class="text-muted" style="font-size:14px">{{ __('login.welcome-back') }}</div>
            </div>

            @if(Session::has('account_deactivated'))
                <div class="card elev-sm" style="border:1px solid #e0645f; color:#e0645f; padding:var(--space-3); font-size:13px">
                    {{ Session::get('account_deactivated') }}
                </div>
            @endif
            <div class="card p-4 border-0 shadow-sm">
                <div class="card-body">
                    <form id="login" method="post" action="{{ url('/login') }}">
                        @csrf
                        <h1>Login</h1>
                        <p class="text-muted">Sign In to your account</p>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                    <span class="input-group-text">
                                      <i class="bi bi-person"></i>
                                    </span>
                            </div>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}"
                                   placeholder="Email">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-group mb-4">
                            <div class="input-group-prepend">
                                    <span class="input-group-text">
                                      <i class="bi bi-lock"></i>
                                    </span>
                            </div>
                            <input id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Password" name="password">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <button id="submit" class="btn btn-primary px-4 d-flex align-items-center"
                                        type="submit">
                                    Login
                                    <div id="spinner" class="spinner-border text-info login-spinner" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </button>
                            </div>
                            <div class="col-8 text-right">
                                <a class="btn btn-link px-0" href="{{ route('password.request') }}">
                                    Forgot password?
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <button id="submit" class="btn btn-primary btn-block" type="submit">{{ __('login.sign-in') }}</button>
            </div>

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
