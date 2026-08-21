{{--
    Triangle POS — primary navigation as a horizontal menu bar.
    Each top-level section is a direct route link. The section's own
    items are rendered inline in a secondary navbar (menu-secondary)
    instead of hover dropdowns.
--}}
<nav class="app-topnav" aria-label="{{ __('menu.products') }}">
    <div class="container-fluid">

        <div class="app-topnav-collapse" id="app-topnav-collapse">
            <ul class="app-topnav-list">
                {{-- Home --}}
                <li class="app-topnav-item {{ request()->routeIs('home') ? 'is-active' : '' }}">
                    <a class="app-topnav-link" href="{{ route('home') }}">
                        <i class="bi bi-house"></i> <span>{{ __('menu.home') }}</span>
                    </a>
                </li>

                @can('access_products')
                <li class="app-topnav-item {{ request()->routeIs('products.*') || request()->routeIs('product-categories.*') || request()->routeIs('barcode.print') ? 'is-active' : '' }}">
                    <a class="app-topnav-link" href="{{ route('products.index') }}">
                        <i class="bi bi-journal-bookmark"></i> <span>{{ __('menu.products') }}</span>
                    </a>
                </li>
                @endcan

                @canany(['access_adjustments', 'access_stock_exits', 'create_stock_exits', 'access_purchases', 'access_purchase_returns', 'access_sales', 'access_sale_returns'])
                <li class="app-topnav-item {{ request()->routeIs('adjustments.*') || request()->routeIs('stock-exits.*') || request()->routeIs('stock-entries.*') || request()->routeIs('purchases.*') || request()->routeIs('purchase-payments*') || request()->routeIs('purchase-returns.*') || request()->routeIs('purchase-return-payments.*') || request()->routeIs('sales.*') || request()->routeIs('sale-payments*') || request()->routeIs('sale-returns.*') || request()->routeIs('sale-return-payments.*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link" href="{{ auth()->user()->can('access_sales') ? route('sales.index') : (auth()->user()->can('access_purchases') ? route('purchases.index') : route('adjustments.index')) }}">
                        <i class="bi bi-arrow-left-right"></i> <span>{{ __('menu.transactions') }}</span>
                    </a>
                </li>
                @endcanany

                @can('access_quotations')
                <li class="app-topnav-item {{ request()->routeIs('quotations.*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link" href="{{ route('quotations.index') }}">
                        <i class="bi bi-cart-check"></i> <span>{{ __('menu.quotations') }}</span>
                    </a>
                </li>
                @endcan

                @canany(['access_bon_commandes', 'access_commandes'])
                <li class="app-topnav-item {{ request()->routeIs('bon-commandes.*') || request()->routeIs('commandes.*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link" href="{{ auth()->user()->can('access_bon_commandes') ? route('bon-commandes.index') : route('commandes.index') }}">
                        <i class="bi bi-clipboard-check"></i> <span>{{ __('menu.orders') }}</span>
                    </a>
                </li>
                @endcanany

                @can('access_expenses')
                <li class="app-topnav-item {{ request()->routeIs('expenses.*') || request()->routeIs('expense-categories.*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link" href="{{ route('expenses.index') }}">
                        <i class="bi bi-wallet2"></i> <span>{{ __('menu.expenses') }}</span>
                    </a>
                </li>
                @endcan

                @canany(['access_customers', 'access_suppliers'])
                <li class="app-topnav-item dropdown {{ request()->routeIs('customers.*') || request()->routeIs('suppliers.*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-people"></i> <span>{{ __('menu.parties') }}</span>
                    </a>
                    <div class="dropdown-menu">
                        @can('access_customers')
                            <a class="dropdown-item {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}"><i class="bi bi-people-fill"></i> <span>{{ __('menu.customers') }}</span></a>
                        @endcan
                        @can('access_suppliers')
                            <a class="dropdown-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}"><i class="bi bi-people-fill"></i> <span>{{ __('menu.suppliers') }}</span></a>
                        @endcan
                    </div>
                </li>
                @endcanany

                @can('access_reports')
                <li class="app-topnav-item {{ request()->routeIs('*-report.index') ? 'is-active' : '' }}">
                    <a class="app-topnav-link" href="{{ route('profit-loss-report.index') }}">
                        <i class="bi bi-graph-up"></i> <span>{{ __('menu.reports') }}</span>
                    </a>
                </li>
                @endcan

                @can('access_user_management')
                <li class="app-topnav-item {{ request()->routeIs('users*') || request()->routeIs('roles*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link" href="{{ route('users.index') }}">
                        <i class="bi bi-people"></i> <span>{{ __('menu.user-management') }}</span>
                    </a>
                </li>
                @endcan

                @can('access_activity_logs')
                <li class="app-topnav-item {{ request()->routeIs('activity-logs.*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link" href="{{ route('activity-logs.index') }}">
                        <i class="bi bi-clock-history"></i> <span>{{ __('menu.activity-logs') }}</span>
                    </a>
                </li>
                @endcan

                @canany(['access_currencies', 'access_settings'])
                <li class="app-topnav-item {{ request()->routeIs('currencies*') || request()->routeIs('units*') || request()->routeIs('settings*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link" href="{{ auth()->user()->can('access_settings') ? route('settings.index') : (auth()->user()->can('access_units') ? route('units.index') : route('currencies.index')) }}">
                        <i class="bi bi-gear"></i> <span>{{ __('menu.settings') }}</span>
                    </a>
                </li>
                @endcanany

                <li class="app-topnav-item {{ request()->routeIs('documentation.*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link" href="{{ route('documentation.index') }}">
                        <i class="bi bi-book"></i> <span>{{ __('menu.documentation') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

@include('layouts.menu-secondary')
