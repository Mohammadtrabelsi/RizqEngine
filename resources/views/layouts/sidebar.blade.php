{{--
    Triangle POS — primary navigation as a vertical sidebar.
    Each top-level section is a direct route link. The section's own
    child items are rendered in the secondary bar (menu-secondary),
    placed beneath the breadcrumb in the content area.
--}}
<aside class="app-sidebar" id="app-sidebar">
    <div class="app-sidebar-brand">
        @php($sidebarSettings = settings())
        <a href="{{ route('home') }}" class="app-sidebar-brand-link">
            @if($sidebarSettings->client_logo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($sidebarSettings->client_logo) }}" alt="{{ $sidebarSettings->client_name ?? 'Logo' }}" class="app-sidebar-logo-img">
                @if($sidebarSettings->client_name)
                    <span class="app-sidebar-brand-name">{{ $sidebarSettings->client_name }}</span>
                @endif
            @elseif($sidebarSettings->client_name)
                <span class="app-sidebar-brand-name">{{ $sidebarSettings->client_name }}</span>
            @else
                @include('layouts.logo', ['size' => 26, 'label' => 'Triangle POS', 'labelSize' => 16, 'stroke' => '#ffffff', 'accent' => '#c7d2fe'])
            @endif
        </a>
        <button class="app-sidebar-close d-lg-none" type="button"
                data-toggle="collapse" data-target="#app-sidebar" aria-label="Close menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="app-sidebar-nav" aria-label="{{ __('menu.products') }}">
        <ul class="app-sidebar-list">
            <li class="app-sidebar-heading">{{ __('menu.home') }}</li>
            <li class="app-sidebar-item {{ request()->routeIs('home') ? 'is-active' : '' }}">
                <a class="app-sidebar-link app-sidebar-link--home" href="{{ route('home') }}">
                    <i class="bi bi-house-door"></i> <span>{{ __('menu.home') }}</span>
                </a>
            </li>

            <li class="app-sidebar-heading">{{ __('menu.transactions') }}</li>
            @can('access_products')
            <li class="app-sidebar-item {{ request()->routeIs('products.*') || request()->routeIs('product-categories.*') || request()->routeIs('barcode.print') ? 'is-active' : '' }}">
                <a class="app-sidebar-link app-sidebar-link--products" href="{{ route('products.index') }}">
                    <i class="bi bi-journal-bookmark"></i> <span>{{ __('menu.products') }}</span>
                </a>
            </li>
            @endcan

            @canany(['access_adjustments', 'access_stock_exits', 'create_stock_exits', 'access_purchases', 'access_purchase_returns', 'access_sales', 'access_sale_returns'])
            <li class="app-sidebar-item {{ request()->routeIs('adjustments.*') || request()->routeIs('stock-exits.*') || request()->routeIs('stock-entries.*') || request()->routeIs('purchases.*') || request()->routeIs('purchase-payments*') || request()->routeIs('purchase-returns.*') || request()->routeIs('purchase-return-payments.*') || request()->routeIs('sales.*') || request()->routeIs('sale-payments*') || request()->routeIs('sale-returns.*') || request()->routeIs('sale-return-payments.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link app-sidebar-link--transactions" href="{{ auth()->user()->can('access_sales') ? route('sales.index') : (auth()->user()->can('access_purchases') ? route('purchases.index') : route('adjustments.index')) }}">
                    <i class="bi bi-arrow-left-right"></i> <span>{{ __('menu.transactions') }}</span>
                </a>
            </li>
            @endcanany

            @can('access_quotations')
            <li class="app-sidebar-item {{ request()->routeIs('quotations.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link app-sidebar-link--quotations" href="{{ route('quotations.index') }}">
                    <i class="bi bi-cart-check"></i> <span>{{ __('menu.quotations') }}</span>
                </a>
            </li>
            @endcan

            @canany(['access_bon_commandes', 'access_commandes'])
            <li class="app-sidebar-item {{ request()->routeIs('bon-commandes.*') || request()->routeIs('commandes.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link app-sidebar-link--orders" href="{{ auth()->user()->can('access_bon_commandes') ? route('bon-commandes.index') : route('commandes.index') }}">
                    <i class="bi bi-clipboard-check"></i> <span>{{ __('menu.orders') }}</span>
                </a>
            </li>
            @endcanany

            @can('access_expenses')
            <li class="app-sidebar-item {{ request()->routeIs('expenses.*') || request()->routeIs('expense-categories.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link app-sidebar-link--expenses" href="{{ route('expenses.index') }}">
                    <i class="bi bi-wallet2"></i> <span>{{ __('menu.expenses') }}</span>
                </a>
            </li>
            @endcan

            @canany(['access_customers', 'access_suppliers'])
            <li class="app-sidebar-heading">{{ __('menu.customers') }} &amp; {{ __('menu.suppliers') }}</li>
            @endcanany
            @can('access_customers')
            <li class="app-sidebar-item {{ request()->routeIs('customers.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link app-sidebar-link--customers" href="{{ route('customers.index') }}">
                    <i class="bi bi-people"></i> <span>{{ __('menu.customers') }}</span>
                </a>
            </li>
            @endcan

            @can('access_suppliers')
            <li class="app-sidebar-item {{ request()->routeIs('suppliers.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link app-sidebar-link--suppliers" href="{{ route('suppliers.index') }}">
                    <i class="bi bi-truck"></i> <span>{{ __('menu.suppliers') }}</span>
                </a>
            </li>
            @endcan

            @can('access_reports')
            <li class="app-sidebar-heading">{{ __('menu.reports') }}</li>
            <li class="app-sidebar-item {{ request()->routeIs('*-report.index') ? 'is-active' : '' }}">
                <a class="app-sidebar-link app-sidebar-link--reports" href="{{ route('profit-loss-report.index') }}">
                    <i class="bi bi-graph-up"></i> <span>{{ __('menu.reports') }}</span>
                </a>
            </li>
            @endcan

            @canany(['access_user_management', 'access_activity_logs', 'access_currencies', 'access_settings'])
            <li class="app-sidebar-heading">{{ __('menu.settings') }}</li>
            @endcanany

            @can('access_user_management')
            <li class="app-sidebar-item {{ request()->routeIs('users*') || request()->routeIs('roles*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link app-sidebar-link--users" href="{{ route('users.index') }}">
                    <i class="bi bi-people-fill"></i> <span>{{ __('menu.user-management') }}</span>
                </a>
            </li>
            @endcan

            @can('access_activity_logs')
            <li class="app-sidebar-item {{ request()->routeIs('activity-logs.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link app-sidebar-link--logs" href="{{ route('activity-logs.index') }}">
                    <i class="bi bi-clock-history"></i> <span>{{ __('menu.activity-logs') }}</span>
                </a>
            </li>
            @endcan

            @canany(['access_currencies', 'access_settings'])
            <li class="app-sidebar-item {{ request()->routeIs('currencies*') || request()->routeIs('units*') || request()->routeIs('settings*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link app-sidebar-link--settings" href="{{ auth()->user()->can('access_settings') ? route('settings.index') : (auth()->user()->can('access_units') ? route('units.index') : route('currencies.index')) }}">
                    <i class="bi bi-gear"></i> <span>{{ __('menu.settings') }}</span>
                </a>
            </li>
            @endcanany

            <li class="app-sidebar-item {{ request()->routeIs('documentation.*') ? 'is-active' : '' }}">
                <a class="app-sidebar-link app-sidebar-link--docs" href="{{ route('documentation.index') }}">
                    <i class="bi bi-book"></i> <span>{{ __('menu.documentation') }}</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
<div class="app-sidebar-backdrop d-lg-none" data-toggle="collapse" data-target="#app-sidebar"></div>
