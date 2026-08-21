<div class="container-fluid d-flex align-items-center app-header-bar bg-white border-bottom">
    <button class="c-header-toggler app-nav-toggler d-lg-none mfe-auto btn btn-ghost text-slate-800" type="button"
            data-toggle="collapse" data-target="#app-topnav-collapse"
            aria-controls="app-topnav-collapse" aria-expanded="false" aria-label="Toggle navigation">
        <i class="bi bi-list icon-fs-2rem"></i>
    </button>

    @php($headerSettings = settings())
    <a href="{{ route('home') }}" class="c-header-brand d-flex align-items-center mfs-3" style="text-decoration:none; gap:8px;">
        @if($headerSettings->client_logo || $headerSettings->client_name)
            @if($headerSettings->client_logo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($headerSettings->client_logo) }}" alt="{{ $headerSettings->client_name ?? 'Logo' }}" style="max-height:26px; max-width:120px;">
            @endif
            @if($headerSettings->client_name)
                <span class="font-weight-bold d-md-down-none text-slate-900" style="font-size:1.05rem;">{{ $headerSettings->client_name }}</span>
            @endif
        @else
            @include('layouts.logo', ['size' => 24, 'label' => 'Triangle POS', 'labelSize' => 15])
        @endif
    </a>

    <ul class="c-header-nav ms-auto align-items-center gap-2">
        <li class="c-header-nav-item d-flex align-items-center">
            @include('includes.language-switcher')
        </li>

        @can('create_pos_sales')
            <li class="c-header-nav-item">
                <a class="btn btn-primary btn-pill btn-sm {{ request()->routeIs('app.pos.index') ? 'disabled' : '' }}" href="{{ route('app.pos.index') }}">
                    <i class="bi bi-cart mr-1"></i> {{ __('app.pos_system') }}
                </a>
            </li>
        @endcan

        @can('show_notifications')
            <li class="c-header-nav-item dropdown d-md-down-none">

                {{-- Bell trigger --}}
                <a id="notif-bell-btn"
                   class="c-header-nav-link position-relative d-flex align-items-center justify-content-center notif-bell-btn"
                   data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="bi bi-bell-fill"></i>
                    @if($low_quantity_products->count() > 0)
                        <span class="notif-badge">
                            {{ $low_quantity_products->count() > 9 ? '9+' : $low_quantity_products->count() }}
                        </span>
                    @endif
                </a>

                {{-- Dropdown panel --}}
                <div class="dropdown-menu dropdown-menu-right p-0 border-0 notif-panel">

                    {{-- Gradient header --}}
                    <div class="notif-panel-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="notif-header-icon">
                                <i class="bi bi-bell-fill"></i>
                            </div>
                            <div>
                                <div class="notif-header-title">{{ __('app.notifications') }}</div>
                                <div class="notif-header-sub">{{ __('app.low-stock-alerts') }}</div>
                            </div>
                        </div>
                        @if($low_quantity_products->count() > 0)
                            <span class="notif-count-badge notif-count-badge--alert">
                                {{ $low_quantity_products->count() }} {{ __('app.alerts') }}
                            </span>
                        @else
                            <span class="notif-count-badge notif-count-badge--ok">
                                {{ __('app.all-clear') }}
                            </span>
                        @endif
                    </div>

                    {{-- Notification rows --}}
                    <div class="notif-list">
                        @forelse($low_quantity_products as $product)
                            <a href="{{ route('products.show', $product->id) }}" class="notif-item">

                                {{-- Severity icon --}}
                                <div class="notif-icon
                                    {{ $product->product_quantity <= $product->product_stock_alert
                                        ? 'notif-icon--critical'
                                        : ($product->product_quantity <= $product->product_stock_alert * 2
                                            ? 'notif-icon--low'
                                            : 'notif-icon--ok') }}">
                                    <i class="bi {{ $product->product_quantity <= $product->product_stock_alert
                                        ? 'bi-exclamation-octagon-fill'
                                        : ($product->product_quantity <= $product->product_stock_alert * 2
                                            ? 'bi-exclamation-triangle-fill'
                                            : 'bi-check-circle-fill') }}"></i>
                                </div>

                                {{-- Product info --}}
                                <div class="notif-info">
                                    <div class="notif-name">{{ $product->product_name }}</div>
                                    <div class="notif-code">
                                        <i class="bi bi-upc-scan"></i> {{ $product->product_code }}
                                    </div>
                                </div>

                                {{-- Quantity --}}
                                <div class="notif-qty">
                                    <span class="notif-qty-num
                                        {{ $product->product_quantity <= $product->product_stock_alert
                                            ? 'notif-qty-num--critical'
                                            : ($product->product_quantity <= $product->product_stock_alert * 2
                                                ? 'notif-qty-num--low'
                                                : 'notif-qty-num--ok') }}">
                                        {{ $product->product_quantity }}
                                    </span>
                                    <span class="notif-unit">{{ $product->product_unit }}</span>
                                </div>

                            </a>
                        @empty
                            <div class="notif-empty">
                                <div class="notif-empty-icon">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="notif-empty-title">{{ __('app.all-good') }}</div>
                                <div class="notif-empty-sub">{{ __('app.no-notifications') }}</div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Footer --}}
                    @if($low_quantity_products->count() > 0)
                        <div class="notif-footer">
                            <a href="{{ route('products.index') }}" class="notif-footer-link">
                                {{ __('app.view-all-products') }}
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    @endif

                </div>
            </li>
        @endcan

        {{-- User dropdown --}}
        <li class="c-header-nav-item dropdown">
            <a class="user-trigger c-header-nav-link" data-toggle="dropdown" href="#"
               role="button" aria-haspopup="true" aria-expanded="false">
                <img class="user-trigger-avatar"
                     src="{{ auth()->user()->getFirstMediaUrl('avatars') }}"
                     alt="{{ auth()->user()->name }}">
                <div class="user-trigger-info">
                    <span class="user-trigger-name">{{ auth()->user()->name }}</span>
                    <span class="user-trigger-status">
                        <i class="bi bi-circle-fill"></i> {{ __('app.online') }}
                    </span>
                </div>
                <i class="bi bi-chevron-down user-trigger-caret"></i>
            </a>

            <div class="dropdown-menu dropdown-menu-right p-0 border-0 user-panel">

                {{-- User card --}}
                <div class="user-card">
                    <img class="user-card-avatar"
                         src="{{ auth()->user()->getFirstMediaUrl('avatars') }}"
                         alt="{{ auth()->user()->name }}">
                    <div>
                        <div class="user-card-name">{{ auth()->user()->name }}</div>
                        <div class="user-card-email">{{ auth()->user()->email }}</div>
                    </div>
                </div>

                {{-- Menu items --}}
                <div class="user-menu">
                    <a class="user-menu-item" href="{{ route('profile.edit') }}">
                        <span class="user-menu-item-icon user-menu-item-icon--indigo">
                            <i class="bi bi-person-fill"></i>
                        </span>
                        {{ __('app.profile') }}
                    </a>
                    <a id="logout-link" class="user-menu-item user-menu-item--danger" href="#">
                        <span class="user-menu-item-icon user-menu-item-icon--red">
                            <i class="bi bi-box-arrow-left"></i>
                        </span>
                        {{ __('app.logout') }}
                    </a>
                </div>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</div>
