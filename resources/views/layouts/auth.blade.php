{{--
    RizqEngine — shared authentication shell.
    Mirrors the redesign sign-in (livewire/auth/login): an ink brand panel on
    the large screens and a light canvas form column, so the password reset /
    confirm flows share the exact chrome as the primary /sign-in screen.
    Pages provide @section('heading'), @section('lead') and @section('form').
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      class="antialiased">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>{{ trim($__env->yieldContent('title')) !== '' ? trim($__env->yieldContent('title')) . ' — ' . config('app.name') : config('app.name') }}</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('images/favicon.png') }}">
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>
<body class="bg-canvas font-body text-ink">
<div class="grid min-h-screen grid-cols-1 lg:grid-cols-[1.1fr_1fr]">
    {{-- Brand panel --}}
    <div class="relative hidden flex-col justify-between overflow-hidden bg-ink px-16 py-14 text-white lg:flex">
        <a href="{{ route('welcome') }}" class="flex items-center gap-3">
            @if($clientLogo = client_logo_url())
                <img src="{{ $clientLogo }}" alt="{{ settings()->client_name ?? 'Client logo' }}" class="h-[26px] w-auto object-contain">
            @endif
            <x-logo-mark tone="dark" />
            <span class="font-display text-lg font-bold">{{ settings()->client_name ?? 'RizqEngine' }}</span>
        </a>

        <div class="max-w-[560px]">
            <h1 class="font-display text-[52px] font-bold leading-none tracking-display">{{ __('login.welcome') }}</h1>
            <p class="mt-6 text-[17px] leading-[1.65] text-white/70 text-pretty">{{ __('login.description') }}</p>
        </div>

        <p class="text-[13px] text-white/45">© {{ date('Y') }} RizqEngine</p>
    </div>

    {{-- Form column --}}
    <div class="grid place-items-center bg-canvas px-6 py-14 lg:px-12">
        <div class="w-full max-w-[420px]">
            @yield('form')
        </div>
    </div>
</div>

@vite('resources/js/app.js')
</body>
</html>
