{{--
    Triangle POS — primary navigation as a vertical sidebar.
    Single-accent ink design, unified with the redesign dashboard shell:
    text items grouped under mono headings, an accent-filled active item, and a
    shift card pinned to the bottom. Sections with more than one destination are
    collapsible so every route/feature is reachable directly from the sidebar —
    mirroring the inline items previously only shown in menu-secondary. Toggling
    is handled by the vanilla `data-toggle="submenu"` handler in app.js.
--}}
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
            <li class="app-sidebar-item {{ $inProducts ? 'is-active' : '' }}">
                <button type="button" class="app-sidebar-link app-sidebar-toggle {{ $inProducts ? 'is-open' : '' }}" data-toggle="submenu" aria-expanded="{{ $inProducts ? 'true' : 'false' }}">
                    <span>{{ __('nav.products') }}</span>
                    <i class="bi bi-chevron-down app-sidebar-caret"></i>
                </button>
                <ul class="app-sidebar-sublist {{ $inProducts ? 'is-open' : '' }}">
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('products.index') ? 'is-active' : '' }}" href="{{ route('products.index') }}">{{ __('menu.all-products') }}</a></li>
                    @can('create_products')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('products.create') ? 'is-active' : '' }}" href="{{ route('products.create') }}">{{ __('menu.create-product') }}</a></li>
                    @endcan
                    @can('access_product_categories')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('product-categories.*') ? 'is-active' : '' }}" href="{{ route('product-categories.index') }}">{{ __('menu.categories') }}</a></li>
                    @endcan
                    @can('print_barcodes')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('barcode.print') ? 'is-active' : '' }}" href="{{ route('barcode.print') }}">{{ __('menu.print-barcode') }}</a></li>
                    @endcan
                </ul>
            </li>
            @endcan

            @canany(['access_adjustments', 'access_stock_exits', 'create_stock_exits', 'access_purchases', 'access_purchase_returns', 'access_sales', 'access_sale_returns'])
            <li class="app-sidebar-item {{ $inTransactions ? 'is-active' : '' }}">
                <button type="button" class="app-sidebar-link app-sidebar-toggle {{ $inTransactions ? 'is-open' : '' }}" data-toggle="submenu" aria-expanded="{{ $inTransactions ? 'true' : 'false' }}">
                    <span>{{ __('nav.transactions') }}</span>
                    <i class="bi bi-chevron-down app-sidebar-caret"></i>
                </button>
                <ul class="app-sidebar-sublist {{ $inTransactions ? 'is-open' : '' }}">
                    @can('access_sales')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('sales.*') || request()->routeIs('sale-payments*') ? 'is-active' : '' }}" href="{{ route('sales.index') }}">{{ __('menu.all-sales') }}</a></li>
                    @endcan
                    @can('access_sale_returns')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('sale-returns.*') || request()->routeIs('sale-return-payments*') ? 'is-active' : '' }}" href="{{ route('sale-returns.index') }}">{{ __('menu.all-sale-returns') }}</a></li>
                    @endcan
                    @can('access_purchases')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('purchases.*') || request()->routeIs('purchase-payments*') ? 'is-active' : '' }}" href="{{ route('purchases.index') }}">{{ __('menu.all-purchases') }}</a></li>
                    @endcan
                    @can('access_purchase_returns')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('purchase-returns.*') || request()->routeIs('purchase-return-payments*') ? 'is-active' : '' }}" href="{{ route('purchase-returns.index') }}">{{ __('menu.all-purchase-returns') }}</a></li>
                    @endcan
                    @can('access_stock_exits')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('stock-exits.*') || request()->routeIs('stock-entries.*') ? 'is-active' : '' }}" href="{{ route('stock-exits.index') }}">{{ __('menu.all-stock-exits') }}</a></li>
                    @endcan
                    @can('access_adjustments')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('adjustments.*') ? 'is-active' : '' }}" href="{{ route('adjustments.index') }}">{{ __('menu.all-adjustments') }}</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany

            @can('access_quotations')
            <li class="app-sidebar-item {{ $inQuotations ? 'is-active' : '' }}">
                <button type="button" class="app-sidebar-link app-sidebar-toggle {{ $inQuotations ? 'is-open' : '' }}" data-toggle="submenu" aria-expanded="{{ $inQuotations ? 'true' : 'false' }}">
                    <span>{{ __('nav.quotes') }}</span>
                    <i class="bi bi-chevron-down app-sidebar-caret"></i>
                </button>
                <ul class="app-sidebar-sublist {{ $inQuotations ? 'is-open' : '' }}">
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('quotations.index') ? 'is-active' : '' }}" href="{{ route('quotations.index') }}">{{ __('menu.all-quotations') }}</a></li>
                    @can('create_adjustments')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('quotations.create') ? 'is-active' : '' }}" href="{{ route('quotations.create') }}">{{ __('menu.create-quotation') }}</a></li>
                    @endcan
                </ul>
            </li>
            @endcan

            @canany(['access_bon_commandes', 'access_commandes'])
            <li class="app-sidebar-item {{ $inOrders ? 'is-active' : '' }}">
                <button type="button" class="app-sidebar-link app-sidebar-toggle {{ $inOrders ? 'is-open' : '' }}" data-toggle="submenu" aria-expanded="{{ $inOrders ? 'true' : 'false' }}">
                    <span>{{ __('nav.orders') }}</span>
                    <i class="bi bi-chevron-down app-sidebar-caret"></i>
                </button>
                <ul class="app-sidebar-sublist {{ $inOrders ? 'is-open' : '' }}">
                    @can('access_bon_commandes')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('bon-commandes.*') ? 'is-active' : '' }}" href="{{ route('bon-commandes.index') }}">{{ __('menu.all-bon-commandes') }}</a></li>
                    @endcan
                    @can('access_commandes')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('commandes.*') ? 'is-active' : '' }}" href="{{ route('commandes.index') }}">{{ __('menu.all-commandes') }}</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany

            @can('access_expenses')
            <li class="app-sidebar-item {{ $inExpenses ? 'is-active' : '' }}">
                <button type="button" class="app-sidebar-link app-sidebar-toggle {{ $inExpenses ? 'is-open' : '' }}" data-toggle="submenu" aria-expanded="{{ $inExpenses ? 'true' : 'false' }}">
                    <span>{{ __('nav.expenses') }}</span>
                    <i class="bi bi-chevron-down app-sidebar-caret"></i>
                </button>
                <ul class="app-sidebar-sublist {{ $inExpenses ? 'is-open' : '' }}">
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('expenses.index') ? 'is-active' : '' }}" href="{{ route('expenses.index') }}">{{ __('menu.all-expenses') }}</a></li>
                    @can('create_expenses')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('expenses.create') ? 'is-active' : '' }}" href="{{ route('expenses.create') }}">{{ __('menu.create-expense') }}</a></li>
                    @endcan
                    @can('access_expense_categories')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('expense-categories.*') ? 'is-active' : '' }}" href="{{ route('expense-categories.index') }}">{{ __('menu.categories') }}</a></li>
                    @endcan
                </ul>
            </li>
            @endcan

            {{-- FINANCE --}}
            @php($inFinance = request()->routeIs('monthly-budgets.*') || request()->routeIs('outings.*') || request()->routeIs('invoice-archive.*'))
            <li class="app-sidebar-heading">{{ __('finance.monthly_budgets') }}</li>
            <li class="app-sidebar-item {{ $inFinance ? 'is-active' : '' }}">
                <button type="button" class="app-sidebar-link app-sidebar-toggle {{ $inFinance ? 'is-open' : '' }}" data-toggle="submenu" aria-expanded="{{ $inFinance ? 'true' : 'false' }}">
                    <span>{{ __('finance.monthly_budgets') }}</span>
                    <i class="bi bi-chevron-down app-sidebar-caret"></i>
                </button>
                <ul class="app-sidebar-sublist {{ $inFinance ? 'is-open' : '' }}">
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('monthly-budgets.*') ? 'is-active' : '' }}" href="{{ route('monthly-budgets.index') }}">{{ __('finance.monthly_budgets') }}</a></li>
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('outings.*') ? 'is-active' : '' }}" href="{{ route('outings.index') }}">{{ __('finance.outings') }}</a></li>
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('invoice-archive.*') ? 'is-active' : '' }}" href="{{ route('invoice-archive.index') }}">{{ __('finance.invoice_archive') }}</a></li>
                </ul>
            </li>

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
            <li class="app-sidebar-item {{ $inUsers ? 'is-active' : '' }}">
                <button type="button" class="app-sidebar-link app-sidebar-toggle {{ $inUsers ? 'is-open' : '' }}" data-toggle="submenu" aria-expanded="{{ $inUsers ? 'true' : 'false' }}">
                    <span>{{ __('nav.staff_roles') }}</span>
                    <i class="bi bi-chevron-down app-sidebar-caret"></i>
                </button>
                <ul class="app-sidebar-sublist {{ $inUsers ? 'is-open' : '' }}">
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('users.index') ? 'is-active' : '' }}" href="{{ route('users.index') }}">{{ __('menu.all-users') }}</a></li>
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('users.create') ? 'is-active' : '' }}" href="{{ route('users.create') }}">{{ __('menu.create-user') }}</a></li>
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('roles*') ? 'is-active' : '' }}" href="{{ route('roles.index') }}">{{ __('menu.roles-permissions') }}</a></li>
                </ul>
            </li>
            @endcan

            {{-- INSIGHT --}}
            @canany(['access_reports', 'access_activity_logs', 'access_currencies', 'access_settings', 'access_units'])
            <li class="app-sidebar-heading">{{ __('nav.group.insight') }}</li>
            @endcanany
            @can('access_reports')
            <li class="app-sidebar-item {{ $inReports ? 'is-active' : '' }}">
                <button type="button" class="app-sidebar-link app-sidebar-toggle {{ $inReports ? 'is-open' : '' }}" data-toggle="submenu" aria-expanded="{{ $inReports ? 'true' : 'false' }}">
                    <span>{{ __('nav.reports') }}</span>
                    <i class="bi bi-chevron-down app-sidebar-caret"></i>
                </button>
                <ul class="app-sidebar-sublist {{ $inReports ? 'is-open' : '' }}">
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('profit-loss-report.index') ? 'is-active' : '' }}" href="{{ route('profit-loss-report.index') }}">{{ __('menu.profit-loss-report') }}</a></li>
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('payments-report.index') ? 'is-active' : '' }}" href="{{ route('payments-report.index') }}">{{ __('menu.payments-report') }}</a></li>
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('sales-report.index') ? 'is-active' : '' }}" href="{{ route('sales-report.index') }}">{{ __('menu.sales-report') }}</a></li>
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('purchases-report.index') ? 'is-active' : '' }}" href="{{ route('purchases-report.index') }}">{{ __('menu.purchases-report') }}</a></li>
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('sales-return-report.index') ? 'is-active' : '' }}" href="{{ route('sales-return-report.index') }}">{{ __('menu.sales-return-report') }}</a></li>
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('purchases-return-report.index') ? 'is-active' : '' }}" href="{{ route('purchases-return-report.index') }}">{{ __('menu.purchases-return-report') }}</a></li>
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('inventory-valuation-report.index') ? 'is-active' : '' }}" href="{{ route('inventory-valuation-report.index') }}">{{ __('menu.inventory-valuation-report') }}</a></li>
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('low-stock-report.index') ? 'is-active' : '' }}" href="{{ route('low-stock-report.index') }}">{{ __('menu.low-stock-report') }}</a></li>
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('stock-movement-report.index') ? 'is-active' : '' }}" href="{{ route('stock-movement-report.index') }}">{{ __('menu.stock-movement-report') }}</a></li>
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('product-movement-report.index') ? 'is-active' : '' }}" href="{{ route('product-movement-report.index') }}">{{ __('menu.product-movement-report') }}</a></li>
                </ul>
            </li>
            @endcan

            @canany(['access_currencies', 'access_settings', 'access_units'])
            <li class="app-sidebar-item {{ $inSettings ? 'is-active' : '' }}">
                <button type="button" class="app-sidebar-link app-sidebar-toggle {{ $inSettings ? 'is-open' : '' }}" data-toggle="submenu" aria-expanded="{{ $inSettings ? 'true' : 'false' }}">
                    <span>{{ __('nav.settings') }}</span>
                    <i class="bi bi-chevron-down app-sidebar-caret"></i>
                </button>
                <ul class="app-sidebar-sublist {{ $inSettings ? 'is-open' : '' }}">
                    @can('access_settings')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('settings*') ? 'is-active' : '' }}" href="{{ route('settings.index') }}">{{ __('menu.system-settings') }}</a></li>
                    @endcan
                    @can('access_units')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('units*') ? 'is-active' : '' }}" href="{{ route('units.index') }}">{{ __('menu.units') }}</a></li>
                    @endcan
                    @can('access_currencies')
                    <li><a class="app-sidebar-sublink {{ request()->routeIs('currencies*') ? 'is-active' : '' }}" href="{{ route('currencies.index') }}">{{ __('menu.currencies') }}</a></li>
                    @endcan
                </ul>
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
