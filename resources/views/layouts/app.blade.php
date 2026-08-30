{{--
    Traditional Blade layout for @extends('layouts.app') pages.
    Uses the exact same sidebar / header / footer partials as the slot-based
    components/layouts/admin.blade.php used by full-page Livewire components,
    so every admin screen shares identical chrome. The page @section('title')
    feeds the shell header.
--}}
@php($__pageTitle = trim($__env->yieldContent('title')))
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      class="antialiased">
<head>
    <meta charset="UTF-8">
    <title>{{ $__pageTitle !== '' ? $__pageTitle . ' — ' . config('app.name') : config('app.name') }}</title>
    <meta content="Fahim Anzam Dip" name="author">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('images/favicon.png') }}">

    @include('includes.main-css')
</head>

<body class="c-app app-with-sidebar min-h-screen bg-canvas-2 font-body text-ink {{ request()->cookie('theme') === 'dark' ? 'c-dark-theme' : '' }}">
    @include('layouts.sidebar')

    <div class="flex min-h-screen flex-col bg-canvas-2 lg:ms-[264px]">
        @include('layouts.header', ['title' => $__pageTitle !== '' ? $__pageTitle : null, 'subtitle' => null])

        <main class="flex-1">
            <div class="container-fluid pt-3">
                @include('utils.flash')
            </div>
            @yield('content')
        </main>

        @include('layouts.footer')
    </div>

    @include('includes.main-js')
</body>
</html>
