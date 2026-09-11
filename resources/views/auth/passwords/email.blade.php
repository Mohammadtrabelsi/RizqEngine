@extends('layouts.auth')

@section('title', __('password.reset'))

@section('form')
    <h2 class="mb-2 font-display text-[36px] font-bold tracking-[-0.03em]">{{ __('password.reset') }}</h2>
    <p class="mb-8 text-[15px] leading-relaxed text-body">{{ __('password.reset-message') }}</p>

    @if (session('status'))
        <div class="mb-6 rounded-field border border-ok/30 bg-ok-bg px-4 py-3 text-[13.5px] font-medium text-ok">
            {{ session('status') }}
        </div>
    @endif

    <form method="post" action="{{ url('/password/email') }}" novalidate>
        @csrf

        <label for="email" class="block text-xs font-bold uppercase tracking-[0.08em] text-ink-3">{{ __('password.email') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" autofocus
               placeholder="you@store.com"
               @class([
                   'mt-2 w-full rounded-field border bg-white px-4 py-3.5 text-[15px] font-medium outline-none transition',
                   'focus:border-accent focus:ring-4 focus:ring-accent/15',
                   'border-danger' => $errors->has('email'),
                   'border-hairline' => ! $errors->has('email'),
               ])>
        @error('email') <p class="mt-1.5 text-[12.5px] text-danger">{{ $message }}</p> @enderror

        <button type="submit"
                class="mt-7 w-full rounded-field bg-accent py-4 text-[15.5px] font-semibold text-white shadow-cta transition-colors hover:bg-accent-hover">
            {{ __('password.send-reset-link') }}
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-body">
        <a href="{{ route('login') }}" class="font-semibold text-accent hover:text-accent-hover">{{ __('login.sign-in') }}</a>
    </p>
@endsection
