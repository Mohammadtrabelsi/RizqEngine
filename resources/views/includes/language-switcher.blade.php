<div class="language-switcher">
    <button type="button" class="language-switcher__toggle" aria-haspopup="true" aria-expanded="false" title="{{ __('app.language') }}">
        <svg class="language-switcher__globe" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.2">
            <circle cx="8" cy="8" r="6.5"></circle>
            <path d="M1.5 8h13M8 1.5c2 2 2 10.5 0 13M8 1.5c-2 2-2 10.5 0 13"></path>
        </svg>
        <span class="language-switcher__current">{{ strtoupper(app()->getLocale()) }}</span>
        <span class="language-switcher__caret"></span>
    </button>
    <ul class="language-switcher__menu" role="menu">
        @foreach(config('app.locale_names') as $code => $label)
            <li role="none">
                <a role="menuitem"
                   class="language-switcher__item {{ app()->getLocale() === $code ? 'is-active' : '' }}"
                   href="{{ route('language.switch', $code) }}">
                    <span>{{ $label }}</span>
                    @if(app()->getLocale() === $code)
                        <span class="language-switcher__check">&checkmark;</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</div>
