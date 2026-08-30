{{--
    Triangle POS — primary navigation as a vertical sidebar.
    Single-accent ink design, unified with the redesign dashboard shell:
    icon-free text items grouped under mono headings, an accent-filled active
    item, and a shift card pinned to the bottom.
--}}
@php($sidebarSettings = settings())
<aside class="app-sidebar" id="app-sidebar">
    <div class="app-sidebar-brand">
        <a href="{{ route('dashboard') }}" class="app-sidebar-brand-link">
            @if($sidebarSettings->client_logo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($sidebarSettings->client_logo) }}" alt="{{ $sidebarSettings->client_name ?? 'Logo' }}" class="app-sidebar-logo-img">
                @if($sidebarSettings->client_name)
                    <span class="app-sidebar-brand-name">{{ $sidebarSettings->client_name }}</span>
                @endif
            @else
                <x-logo-mark tone="dark" />
                <span class="app-sidebar-brand-name">{{ $sidebarSettings->client_name ?? 'Triangle POS' }}</span>
            @endif
        </a>
        <button class="app-sidebar-close d-lg-none" type="button"
                data-toggle="collapse" data-target="#app-sidebar" aria-label="Close menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="app-sidebar-nav" aria-label="{{ __('menu.products') }}">
        <ul class="app-sidebar-list">
            {{-- OVERVIEW --}}
            <li class="app-sidebar-heading">{{ __('nav.group.overview') }}</li>
            <li class="app-sidebar-item {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'is-active' : '' }}">
                <a class="app-sidebar-link" href="{{ route('dashboard') }}">
                    <span class="app-sidebar-dot"></span> <span>{{ __('nav.dashboard') }}</span>
                </a>
            </li>
            @can('create_pos_sales')
            <li class="app-sidebar-item {{ request()->routeIs('app.pos.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link" href="{{ route('app.pos.index') }}">
                    <span class="app-sidebar-dot"></span> <span>{{ __('nav.point_of_sale') }}</span>
                </a>
            </li>
            @endcan

            {{-- TRANSACTIONS --}}
            <li class="app-sidebar-heading">{{ __('nav.group.transactions') }}</li>
            @can('access_products')
            <li class="app-sidebar-item {{ request()->routeIs('products.*') || request()->routeIs('product-categories.*') || request()->routeIs('barcode.print') ? 'is-active' : '' }}">
                <a class="app-sidebar-link" href="{{ route('products.index') }}">
                    <span>{{ __('nav.products') }}</span>
                </a>
            </li>
            @endcan

            @canany(['access_adjustments', 'access_stock_exits', 'create_stock_exits', 'access_purchases', 'access_purchase_returns', 'access_sales', 'access_sale_returns'])
            <li class="app-sidebar-item {{ request()->routeIs('adjustments.*') || request()->routeIs('stock-exits.*') || request()->routeIs('stock-entries.*') || request()->routeIs('purchases.*') || request()->routeIs('purchase-payments*') || request()->routeIs('purchase-returns.*') || request()->routeIs('purchase-return-payments.*') || request()->routeIs('sales.*') || request()->routeIs('sale-payments*') || request()->routeIs('sale-returns.*') || request()->routeIs('sale-return-payments.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link" href="{{ auth()->user()->can('access_sales') ? route('sales.index') : (auth()->user()->can('access_purchases') ? route('purchases.index') : route('adjustments.index')) }}">
                    <span>{{ __('nav.transactions') }}</span>
                </a>
            </li>
            @endcanany

            @can('access_quotations')
            <li class="app-sidebar-item {{ request()->routeIs('quotations.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link" href="{{ route('quotations.index') }}">
                    <span>{{ __('nav.quotes') }}</span>
                </a>
            </li>
            @endcan

            @canany(['access_bon_commandes', 'access_commandes'])
            <li class="app-sidebar-item {{ request()->routeIs('bon-commandes.*') || request()->routeIs('commandes.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link" href="{{ auth()->user()->can('access_bon_commandes') ? route('bon-commandes.index') : route('commandes.index') }}">
                    <span>{{ __('nav.orders') }}</span>
                </a>
            </li>
            @endcanany

            @can('access_expenses')
            <li class="app-sidebar-item {{ request()->routeIs('expenses.*') || request()->routeIs('expense-categories.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link" href="{{ route('expenses.index') }}">
                    <span>{{ __('nav.expenses') }}</span>
                </a>
            </li>
            @endcan

            {{-- PEOPLE --}}
            @canany(['access_customers', 'access_suppliers', 'access_user_management'])
            <li class="app-sidebar-heading">{{ __('nav.group.people') }}</li>
            @endcanany
            @can('access_customers')
            <li class="app-sidebar-item {{ request()->routeIs('customers.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link" href="{{ route('customers.index') }}">
                    <span>{{ __('nav.customers') }}</span>
                </a>
            </li>
            @endcan

            @can('access_suppliers')
            <li class="app-sidebar-item {{ request()->routeIs('suppliers.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link" href="{{ route('suppliers.index') }}">
                    <span>{{ __('nav.suppliers') }}</span>
                </a>
            </li>
            @endcan

            @can('access_user_management')
            <li class="app-sidebar-item {{ request()->routeIs('users*') || request()->routeIs('roles*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link" href="{{ route('users.index') }}">
                    <span>{{ __('nav.staff_roles') }}</span>
                </a>
            </li>
            @endcan

            {{-- INSIGHT --}}
            @canany(['access_reports', 'access_activity_logs', 'access_currencies', 'access_settings'])
            <li class="app-sidebar-heading">{{ __('nav.group.insight') }}</li>
            @endcanany
            @can('access_reports')
            <li class="app-sidebar-item {{ request()->routeIs('*-report.index') ? 'is-active' : '' }}">
                <a class="app-sidebar-link" href="{{ route('profit-loss-report.index') }}">
                    <span>{{ __('nav.reports') }}</span>
                </a>
            </li>
            @endcan

            @canany(['access_currencies', 'access_settings'])
            <li class="app-sidebar-item {{ request()->routeIs('currencies*') || request()->routeIs('units*') || request()->routeIs('settings*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link" href="{{ auth()->user()->can('access_settings') ? route('settings.index') : (auth()->user()->can('access_units') ? route('units.index') : route('currencies.index')) }}">
                    <span>{{ __('nav.settings') }}</span>
                </a>
            </li>
            @endcanany

            @can('access_activity_logs')
            <li class="app-sidebar-item {{ request()->routeIs('activity-logs.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link" href="{{ route('activity-logs.index') }}">
                    <span>{{ __('menu.activity-logs') }}</span>
                </a>
            </li>
            @endcan

            <li class="app-sidebar-item {{ request()->routeIs('documentation.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link" href="{{ route('documentation.index') }}">
                    <span>{{ __('menu.documentation') }}</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="app-sidebar-shift">
        <p class="app-sidebar-shift-label">{{ __('dash.shift_open') }}</p>
        <p class="app-sidebar-shift-time">08:12 — {{ __('dash.now') }}</p>
        <p class="app-sidebar-shift-meta">{{ __('dash.register_role', ['register' => '01', 'role' => auth()->user()->role_label]) }}</p>
    </div>
</aside>
<div class="app-sidebar-backdrop d-lg-none" data-toggle="collapse" data-target="#app-sidebar"></div>
