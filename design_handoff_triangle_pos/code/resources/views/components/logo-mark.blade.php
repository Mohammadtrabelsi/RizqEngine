{{-- resources/views/components/logo-mark.blade.php --}}
@props(['tone' => 'light'])

<span @class([
    'grid h-[30px] w-[30px] place-items-center rounded-[9px]',
    'bg-ink' => $tone === 'light',
    'bg-ink-2' => $tone === 'dark',
])>
    {{-- Replace with the real SVG mark when available --}}
    <span class="h-0 w-0 border-x-[7px] border-b-[12px] border-x-transparent border-b-accent"></span>
</span>
