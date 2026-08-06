{{--
    Triangle POS logo mark — a triangle outline subdivided into four smaller
    triangles, the centre one filled with the accent colour.

    Props:
      $size    icon width in px (height is scaled from the 100x90 viewBox). Default 24.
      $stroke  outline colour. Default the current theme text colour.
      $accent  centre-fill colour. Default the accent.
      $label   optional wordmark rendered beside the mark.
      $labelSize  wordmark font-size in px. Default 15.
--}}
@php
    $size = $size ?? 24;
    $stroke = $stroke ?? 'var(--color-text)';
    $accent = $accent ?? 'var(--color-accent)';
    $sw = $sw ?? 7;
    $height = round($size * 0.9);
@endphp
<span style="display:inline-flex; align-items:center; gap:var(--space-2, 6px); line-height:1;">
    <svg width="{{ $size }}" height="{{ $height }}" viewBox="0 0 100 90" aria-hidden="true">
        <polygon points="50,5 27.5,47.5 72.5,47.5" fill="none" stroke="{{ $stroke }}" stroke-width="{{ $sw }}" stroke-linejoin="round"></polygon>
        <polygon points="5,90 27.5,47.5 50,90" fill="none" stroke="{{ $stroke }}" stroke-width="{{ $sw }}" stroke-linejoin="round"></polygon>
        <polygon points="95,90 72.5,47.5 50,90" fill="none" stroke="{{ $stroke }}" stroke-width="{{ $sw }}" stroke-linejoin="round"></polygon>
        <polygon points="27.5,47.5 72.5,47.5 50,90" fill="{{ $accent }}" stroke="{{ $accent }}" stroke-width="{{ $sw }}" stroke-linejoin="round"></polygon>
    </svg>
    @isset($label)
        <span style="font-family:var(--font-heading, 'Inter',sans-serif); font-weight:500; font-size:{{ $labelSize ?? 15 }}px; letter-spacing:0.01em;">{{ $label }}</span>
    @endisset
</span>
