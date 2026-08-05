<div class="dropdown language-switcher">
    <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
        <i class="bi bi-globe" style="font-size: 18px;"></i>
        <span class="ml-1">{{ strtoupper(app()->getLocale()) }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-right pt-0">
        @foreach(config('app.available_locales') as $code => $locale)
            <a class="dropdown-item {{ app()->getLocale() === $code ? 'active' : '' }}" href="{{ route('lang.switch', $code) }}">
                {{ $locale['native'] }}
            </a>
        @endforeach
    </div>
</div>
