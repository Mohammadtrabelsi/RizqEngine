{{-- resources/views/livewire/auth/login.blade.php --}}
<div class="grid min-h-screen grid-cols-1 lg:grid-cols-[1.1fr_1fr]">
    <div class="hidden flex-col justify-between overflow-hidden bg-ink px-16 py-14 text-white lg:flex">
        <div class="flex items-center gap-3">
            <x-logo-mark tone="dark" />
            <span class="font-display text-lg font-bold">RizqEngine</span>
        </div>

        <div class="max-w-[560px]">
            <h1 class="font-display text-[60px] font-bold leading-none tracking-display">{{ __('auth.hero_title') }}</h1>
            <p class="mt-6 text-[17px] leading-[1.65] text-white/70 text-pretty">{{ __('auth.hero_lead') }}</p>

            <div class="mt-9 flex flex-wrap gap-2.5">
                @foreach (['multi_store', 'offline_till', 'audit_trail'] as $chip)
                    <span class="rounded-full border border-white/20 px-3.5 py-2 font-mono text-[11px] tracking-[0.1em] text-white/80">
                        {{ __("auth.chip.$chip") }}
                    </span>
                @endforeach
            </div>
        </div>

        <p class="text-[13px] text-white/45">© {{ now()->year }} RizqEngine</p>
    </div>

    <div class="grid place-items-center bg-canvas px-6 py-14 lg:px-12">
        <div class="w-full max-w-[420px]">
            <div class="flex justify-end"><livewire:locale-switcher /></div>

            <h2 class="mb-2 mt-7 font-display text-[40px] font-bold tracking-[-0.03em]">{{ __('auth.sign_in') }}</h2>
            <p class="mb-8 text-[15px] text-body">{{ __('auth.sign_in_lead') }}</p>

            <form wire:submit="login" novalidate>
                <label for="email" class="block text-xs font-bold uppercase tracking-[0.08em] text-ink-3">{{ __('auth.email') }}</label>
                <input id="email" type="email" autocomplete="username" wire:model.blur="email"
                       placeholder="you@store.com"
                       @class([
                           'mt-2 w-full rounded-field border bg-white px-4 py-3.5 text-[15px] font-medium outline-none transition',
                           'focus:border-accent focus:ring-4 focus:ring-accent/15',
                           'border-danger' => $errors->has('email'),
                           'border-hairline' => ! $errors->has('email'),
                       ])>
                @error('email') <p class="mt-1.5 text-[12.5px] text-danger">{{ $message }}</p> @enderror

                <div class="mt-[22px] flex items-baseline justify-between">
                    <label for="password" class="text-xs font-bold uppercase tracking-[0.08em] text-ink-3">{{ __('auth.password') }}</label>
                    @if (\Illuminate\Support\Facades\Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[13px] font-semibold text-accent hover:text-accent-hover">{{ __('auth.forgot') }}</a>
                    @endif
                </div>
                <input id="password" type="password" autocomplete="current-password" wire:model="password"
                       placeholder="••••••••"
                       @class([
                           'mt-2 w-full rounded-field border bg-white px-4 py-3.5 text-[15px] font-medium outline-none transition',
                           'focus:border-accent focus:ring-4 focus:ring-accent/15',
                           'border-danger' => $errors->has('password'),
                           'border-hairline' => ! $errors->has('password'),
                       ])>
                @error('password') <p class="mt-1.5 text-[12.5px] text-danger">{{ $message }}</p> @enderror

                <label class="mt-5 flex cursor-pointer items-center gap-2.5 text-sm text-ink-3">
                    <input type="checkbox" wire:model="remember" class="h-[17px] w-[17px] accent-accent">
                    {{ __('auth.remember_register') }}
                </label>

                <button type="submit" wire:loading.attr="disabled" wire:target="login"
                        class="mt-7 w-full rounded-field bg-accent py-4 text-[15.5px] font-semibold text-white shadow-cta transition-colors hover:bg-accent-hover disabled:opacity-70">
                    <span wire:loading.remove wire:target="login">{{ __('auth.sign_in') }}</span>
                    <span wire:loading wire:target="login">{{ __('auth.signing_in') }}</span>
                </button>
            </form>

            <div class="my-7 flex items-center gap-3.5">
                <span class="h-px flex-1 bg-hairline"></span>
                <span class="font-mono text-[11px] tracking-[0.1em] text-muted">{{ __('auth.or') }}</span>
                <span class="h-px flex-1 bg-hairline"></span>
            </div>

            <a href="{{ \Illuminate\Support\Facades\Route::has('login.pin') ? route('login.pin') : '#' }}"
               class="block w-full rounded-field border border-hairline bg-white py-3.5 text-center text-[14.5px] font-semibold text-ink transition-colors hover:border-accent hover:text-accent">
                {{ __('auth.use_pin') }}
            </a>

            <p class="mt-7 text-center text-sm text-body">
                {{ __('auth.need_access') }}
                <a href="{{ \Illuminate\Support\Facades\Route::has('contact.owner') ? route('contact.owner') : '#' }}" class="font-semibold text-accent hover:text-accent-hover">{{ __('auth.contact_owner') }}</a>
            </p>
        </div>
    </div>
</div>
