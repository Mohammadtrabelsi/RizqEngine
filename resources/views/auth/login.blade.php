<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>Sign in | {{ config('app.name') }}</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/nocturne.css') }}">
    <style>
        .login-grid { min-height: 100vh; display: grid; grid-template-columns: 1fr 1fr;
            background: var(--color-bg); color: var(--color-text); }
        .login-brand { background: linear-gradient(160deg, var(--color-surface), var(--color-bg));
            display: flex; flex-direction: column; justify-content: space-between; padding: var(--space-8); }
        .login-form-wrap { display: flex; align-items: center; justify-content: center; padding: var(--space-8); }
        .login-form { width: 100%; max-width: 360px; display: flex; flex-direction: column; gap: var(--space-6); }
        .field-error { color: #e0645f; font-size: 12px; margin-top: 4px; }
        @media (max-width: 820px) {
            .login-grid { grid-template-columns: 1fr; }
            .login-brand { display: none; }
        }
    </style>
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
            <div style="font-family:var(--font-heading); font-weight:500; font-size:32px; line-height:1.2; margin-bottom:var(--space-4)">Run your whole store from one place.</div>
            <div style="font-size:15px; line-height:1.6; color:var(--color-neutral-300)">Products, inventory, purchases, sales, returns, expenses and staff — with reporting and role-based access built in.</div>
        </div>

        <div class="text-muted" style="font-size:12px">© {{ date('Y') }} Triangle POS</div>
    </div>

    <div class="login-form-wrap">
        <form id="login" class="login-form" method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <div style="font-family:var(--font-heading); font-weight:500; font-size:24px; margin-bottom:var(--space-2)">Sign in</div>
                <div class="text-muted" style="font-size:14px">Welcome back. Enter your details to continue.</div>
            </div>

            @if(Session::has('account_deactivated'))
                <div class="card elev-sm" style="border:1px solid #e0645f; color:#e0645f; padding:var(--space-3); font-size:13px">
                    {{ Session::get('account_deactivated') }}
                </div>
            @endif

            <div style="display:flex; flex-direction:column; gap:var(--space-4)">
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" class="input" placeholder="you@store.com"
                           value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" class="input" placeholder="••••••••" required>
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between; font-size:13px">
                    <label class="radio">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="dot" style="border-radius:4px"></span>
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>

                <button id="submit" class="btn btn-primary btn-block" type="submit">Sign in</button>
            </div>

            <div class="text-muted" style="font-size:13px; text-align:center">
                Need access? <a href="{{ route('welcome') }}">Contact your store owner</a>
            </div>
        </form>
    </div>

</div>

<script>
    document.getElementById('login').addEventListener('submit', function () {
        var btn = document.getElementById('submit');
        btn.disabled = true;
        btn.textContent = 'Signing in…';
    });
</script>
</body>
</html>
