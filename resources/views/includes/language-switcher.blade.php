@php
    $languages = [
        'en' => 'English',
        'fr' => 'Français',
        'ar' => 'العربية',
    ];
    $current = app()->getLocale();
@endphp

@once
    <style>
        .language-switcher { position: relative; display: inline-block; font-size: 14px; text-align: left; }
        .language-switcher__toggle {
            display: inline-flex; align-items: center; gap: 6px; cursor: pointer;
            background: transparent; border: 1px solid rgba(128,128,128,.35);
            border-radius: 6px; padding: 6px 10px; color: inherit; line-height: 1; font: inherit;
        }
        .language-switcher__toggle:hover { border-color: rgba(128,128,128,.6); }
        .language-switcher__globe { width: 16px; height: 16px; flex: 0 0 auto; }
        .language-switcher__current { font-weight: 600; }
        .language-switcher__caret { border: solid currentColor; border-width: 0 1.5px 1.5px 0;
            display: inline-block; padding: 2.5px; transform: rotate(45deg); margin-left: 2px; opacity: .7; }
        .language-switcher__menu {
            position: absolute; right: 0; top: calc(100% + 6px); min-width: 160px; margin: 0;
            padding: 6px 0; list-style: none; background: #fff; color: #23282c;
            border: 1px solid rgba(0,0,0,.12); border-radius: 8px;
            box-shadow: 0 6px 24px rgba(0,0,0,.18); z-index: 1050; display: none;
        }
        .language-switcher.is-open .language-switcher__menu { display: block; }
        .language-switcher__item {
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            padding: 8px 14px; color: #23282c; text-decoration: none; white-space: nowrap;
        }
        .language-switcher__item:hover { background: rgba(0,0,0,.06); color: #23282c; text-decoration: none; }
        .language-switcher__item.is-active { font-weight: 600; }
        .language-switcher__check { color: #2eb85c; font-weight: 700; }
    </style>
    <script>
        document.addEventListener('click', function (e) {
            var toggle = e.target.closest('.language-switcher__toggle');
            document.querySelectorAll('.language-switcher.is-open').forEach(function (el) {
                if (!toggle || toggle.parentElement !== el) { el.classList.remove('is-open'); }
            });
            if (toggle) {
                var wrap = toggle.parentElement;
                var open = wrap.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            }
        });
    </script>
@endonce

<div class="language-switcher">
    <button type="button" class="language-switcher__toggle" aria-haspopup="true" aria-expanded="false" title="{{ __('app.language') }}">
        <svg class="language-switcher__globe" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.2">
            <circle cx="8" cy="8" r="6.5"></circle>
            <path d="M1.5 8h13M8 1.5c2 2 2 10.5 0 13M8 1.5c-2 2-2 10.5 0 13"></path>
        </svg>
        <span class="language-switcher__current">{{ strtoupper($current) }}</span>
        <span class="language-switcher__caret"></span>
    </button>
    <ul class="language-switcher__menu" role="menu">
        @foreach($languages as $code => $label)
            <li role="none">
                <a role="menuitem"
                   class="language-switcher__item {{ $current === $code ? 'is-active' : '' }}"
                   href="{{ route('language.switch', $code) }}">
                    <span>{{ $label }}</span>
                    @if($current === $code)
                        <span class="language-switcher__check">&checkmark;</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</div>
