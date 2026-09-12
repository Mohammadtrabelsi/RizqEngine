{{-- resources/views/components/form-card.blade.php --}}
{{--
    Standardised form card: a card with an aligned header and a footer that
    holds the form actions. Keeps every form across the app visually
    consistent, responsive, and with the primary action anchored in the
    card footer.

    Usage:
        <form wire:submit="save">
            <x-form-card :title="__('customer.create')" icon="bi-person">
                {{-- form fields --}}
                <x-slot:footer>
                    <button type="submit" class="btn btn-primary">{{ __('app.save') }}</button>
                </x-slot:footer>
            </x-form-card>
        </form>

    Props:
        title  - optional header title text.
        icon   - optional Bootstrap icon class (e.g. "bi-person").
        footerAlign - horizontal alignment of footer actions:
                      "end" (default), "start", "center", "between".
--}}
@props([
    'title' => null,
    'icon' => null,
    'footerAlign' => 'end',
])

@php
    $justify = [
        'end' => 'justify-content-end',
        'start' => 'justify-content-start',
        'center' => 'justify-content-center',
        'between' => 'justify-content-between',
    ][$footerAlign] ?? 'justify-content-end';
@endphp

<div {{ $attributes->class(['card border-0 shadow-sm mb-4']) }}>
    @if ($title || isset($header))
        <div class="px-4 px-lg-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
            @isset($header)
                {{ $header }}
            @else
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    @if ($icon)<i class="bi {{ $icon }} text-primary"></i>@endif
                    <span>{{ $title }}</span>
                </h5>
            @endisset
        </div>
    @endif

    <div class="flex-auto p-2 p-lg-3">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 d-flex flex-wrap align-items-center gap-2 {{ $justify }}">
            {{ $footer }}
        </div>
    @endisset
</div>
