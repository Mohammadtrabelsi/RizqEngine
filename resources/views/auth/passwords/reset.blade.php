@extends('layouts.auth')

@section('title', __('password.reset'))

@section('form')
    <h2 class="mb-2 font-display text-[36px] font-bold tracking-[-0.03em]">{{ __('password.reset') }}</h2>
    <p class="mb-8 text-[15px] leading-relaxed text-body">{{ __('password.reset-message') }}</p>

    <form method="post" action="{{ url('/password/reset') }}" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token ?? request()->route('token') }}">

        <label for="email" class="block text-xs font-bold uppercase tracking-[0.08em] text-ink-3">{{ __('password.email') }}</label>
        <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" autocomplete="username" autofocus
               placeholder="you@store.com"
               @class([
                   'mt-2 w-full rounded-field border bg-white px-4 py-3.5 text-[15px] font-medium outline-none transition',
                   'focus:border-accent focus:ring-4 focus:ring-accent/15',
                   'border-danger' => $errors->has('email'),
                   'border-hairline' => ! $errors->has('email'),
               ])>
        @error('email') <p class="mt-1.5 text-[12.5px] text-danger">{{ $message }}</p> @enderror

        <label for="password" class="mt-[22px] block text-xs font-bold uppercase tracking-[0.08em] text-ink-3">{{ __('password.password') }}</label>
        <input id="password" type="password" name="password" autocomplete="new-password"
               placeholder="••••••••"
               @class([
                   'mt-2 w-full rounded-field border bg-white px-4 py-3.5 text-[15px] font-medium outline-none transition',
                   'focus:border-accent focus:ring-4 focus:ring-accent/15',
                   'border-danger' => $errors->has('password'),
                   'border-hairline' => ! $errors->has('password'),
               ])>
        @error('password') <p class="mt-1.5 text-[12.5px] text-danger">{{ $message }}</p> @enderror

        <label for="password_confirmation" class="mt-[22px] block text-xs font-bold uppercase tracking-[0.08em] text-ink-3">{{ __('password.confirm-password') }}</label>
        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
               placeholder="••••••••"
               class="mt-2 w-full rounded-field border border-hairline bg-white px-4 py-3.5 text-[15px] font-medium outline-none transition focus:border-accent focus:ring-4 focus:ring-accent/15">

        <button type="submit"
                class="mt-7 w-full rounded-field bg-accent py-4 text-[15.5px] font-semibold text-white shadow-cta transition-colors hover:bg-accent-hover">
            {{ __('password.reset') }}
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-body">
        <a href="{{ route('login') }}" class="font-semibold text-accent hover:text-accent-hover">{{ __('login.sign-in') }}</a>
    </p>
@endsection
