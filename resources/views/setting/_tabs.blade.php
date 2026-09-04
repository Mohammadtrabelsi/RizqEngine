<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('settings.general') ? 'active' : '' }}" href="{{ route('settings.general') }}">
            {{ __('settings.general_settings') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('settings.mail') ? 'active' : '' }}" href="{{ route('settings.mail') }}">
            {{ __('settings.mail_settings') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('settings.images') ? 'active' : '' }}" href="{{ route('settings.images') }}">
            {{ __('settings.default_product_image') }}
        </a>
    </li>
</ul>
