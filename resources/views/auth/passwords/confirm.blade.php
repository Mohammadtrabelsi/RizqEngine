@extends('layouts.auth')

@section('title', __('password.confirm'))

@section('form')
    <h2 class="mb-2 font-display text-[36px] font-bold tracking-[-0.03em]">{{ __('password.confirm') }}</h2>
    <p class="mb-8 text-[15px] leading-relaxed text-body">{{ __('password.confirm-message') }}</p>

    <form method="POST" action="{{ route('password.confirm') }}" novalidate>
        @csrf

        <label for="password" class="block text-xs font-bold uppercase tracking-[0.08em] text-ink-3">{{ __('password.password') }}</label>
        <input id="password" type="password" name="password" autocomplete="current-password" required autofocus
               placeholder="••••••••"
               @class([
                   'mt-2 w-full rounded-field border bg-white px-4 py-3.5 text-[15px] font-medium outline-none transition',
                   'focus:border-accent focus:ring-4 focus:ring-accent/15',
                   'border-danger' => $errors->has('password'),
                   'border-hairline' => ! $errors->has('password'),
               ])>
        @error('password') <p class="mt-1.5 text-[12.5px] text-danger">{{ $message }}</p> @enderror

        <button type="submit"
                class="mt-7 w-full rounded-field bg-accent py-4 text-[15.5px] font-semibold text-white shadow-cta transition-colors hover:bg-accent-hover">
            {{ __('password.confirm') }}
        </button>
    </form>

    @if (\Illuminate\Support\Facades\Route::has('password.request'))
        <p class="mt-7 text-center text-sm text-body">
            <a href="{{ route('password.request') }}" class="font-semibold text-accent hover:text-accent-hover">{{ __('password.forgot') }}</a>
        </p>
    @endif
@endsection
