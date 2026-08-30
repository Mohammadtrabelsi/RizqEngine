{{-- resources/views/livewire/locale-switcher.blade.php --}}
<div class="flex items-center rounded-ctl border border-hairline bg-white p-0.5 font-mono text-[11px] font-semibold">
    @foreach ($locales as $locale)
        <a href="{{ route('language.switch', $locale) }}"
           @class([
               'rounded-[7px] px-2.5 py-1.5 uppercase tracking-[0.08em] transition-colors',
               'bg-accent text-white' => $current === $locale,
               'text-ink-3 hover:text-accent' => $current !== $locale,
           ])>{{ $locale }}</a>
    @endforeach
</div>
