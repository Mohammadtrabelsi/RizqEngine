<span class="logo">
    <svg width="{{ $size }}" height="{{ $height }}" viewBox="0 0 100 90" aria-hidden="true">
        <polygon points="50,5 27.5,47.5 72.5,47.5" fill="none" stroke="{{ $stroke }}" stroke-width="{{ $sw }}" stroke-linejoin="round"></polygon>
        <polygon points="5,90 27.5,47.5 50,90" fill="none" stroke="{{ $stroke }}" stroke-width="{{ $sw }}" stroke-linejoin="round"></polygon>
        <polygon points="95,90 72.5,47.5 50,90" fill="none" stroke="{{ $stroke }}" stroke-width="{{ $sw }}" stroke-linejoin="round"></polygon>
        <polygon points="27.5,47.5 72.5,47.5 50,90" fill="{{ $accent }}" stroke="{{ $accent }}" stroke-width="{{ $sw }}" stroke-linejoin="round"></polygon>
    </svg>
    @isset($label)
        <span class="logo-wordmark">{{ $label }}</span>
    @endisset
</span>
