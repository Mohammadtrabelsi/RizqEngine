{{--
    Triangle POS — primary navigation as a horizontal menu bar.
    Rendered inside the content area (below the header) instead of a
    vertical sidebar. Authored with Tailwind CSS utilities and components.
--}}
<nav class="app-topnav" aria-label="{{ __('menu.products') }}">
    <div class="container">
        <button class="app-topnav-toggler btn btn-sm" type="button"
                data-toggle="collapse" data-target="#app-topnav-collapse"
                aria-controls="app-topnav-collapse" aria-expanded="false"
                aria-label="Toggle navigation">
            <i class="bi bi-list"></i> <span>Menu</span>
        </button>

        <div class="app-topnav-collapse" id="app-topnav-collapse">
            <ul class="app-topnav-list">
                {{-- Home --}}
                <li class="app-topnav-item {{ request()->routeIs('home') ? 'is-active' : '' }}">
                    <a class="app-topnav-link" href="{{ route('home') }}">
                        <i class="bi bi-house"></i> <span>Home</span>
                    </a>
                </li>

                @can('access_products')
                <li class="app-topnav-item dropdown {{ request()->routeIs('products.*') || request()->routeIs('product-categories.*') || request()->routeIs('barcode.print') ? 'is-active' : '' }}">
                    <a class="app-topnav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-journal-bookmark"></i> <span>{{ __('menu.products') }}</span>
                    </a>
                    <div class="dropdown-menu">
                        @can('access_product_categories')
                            <a class="dropdown-item {{ request()->routeIs('product-categories.*') ? 'active' : '' }}" href="{{ route('product-categories.index') }}"><i class="bi bi-collection mr-2"></i>{{ __('menu.categories') }}</a>
                        @endcan
                        @can('create_products')
                            <a class="dropdown-item {{ request()->routeIs('products.create') ? 'active' : '' }}" href="{{ route('products.create') }}"><i class="bi bi-journal-plus mr-2"></i>{{ __('menu.create-product') }}</a>
                        @endcan
                        <a class="dropdown-item {{ request()->routeIs('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}"><i class="bi bi-journals mr-2"></i>{{ __('menu.all-products') }}</a>
                        @can('print_barcodes')
                            <a class="dropdown-item {{ request()->routeIs('barcode.print') ? 'active' : '' }}" href="{{ route('barcode.print') }}"><i class="bi bi-printer mr-2"></i>{{ __('menu.print-barcode') }}</a>
                        @endcan
                    </div>
                </li>
                @endcan

                @can('access_adjustments')
                <li class="app-topnav-item dropdown {{ request()->routeIs('adjustments.*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-clipboard-check"></i> <span>{{ __('menu.stock-adjustments') }}</span>
                    </a>
                    <div class="dropdown-menu">
                        @can('create_adjustments')
                            <a class="dropdown-item {{ request()->routeIs('adjustments.create') ? 'active' : '' }}" href="{{ route('adjustments.create') }}"><i class="bi bi-journal-plus mr-2"></i>{{ __('menu.create-adjustment') }}</a>
                        @endcan
                        <a class="dropdown-item {{ request()->routeIs('adjustments.index') ? 'active' : '' }}" href="{{ route('adjustments.index') }}"><i class="bi bi-journals mr-2"></i>{{ __('menu.all-adjustments') }}</a>
                    </div>
                </li>
                @endcan

                @can('access_quotations')
                <li class="app-topnav-item dropdown {{ request()->routeIs('quotations.*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-cart-check"></i> <span>{{ __('menu.quotations') }}</span>
                    </a>
                    <div class="dropdown-menu">
                        @can('create_adjustments')
                            <a class="dropdown-item {{ request()->routeIs('quotations.create') ? 'active' : '' }}" href="{{ route('quotations.create') }}"><i class="bi bi-journal-plus mr-2"></i>{{ __('menu.create-quotation') }}</a>
                        @endcan
                        <a class="dropdown-item {{ request()->routeIs('quotations.index') ? 'active' : '' }}" href="{{ route('quotations.index') }}"><i class="bi bi-journals mr-2"></i>{{ __('menu.all-quotations') }}</a>
                    </div>
                </li>
                @endcan

                @can('access_purchases')
                <li class="app-topnav-item dropdown {{ request()->routeIs('purchases.*') || request()->routeIs('purchase-payments*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-bag"></i> <span>{{ __('menu.purchases') }}</span>
                    </a>
                    <div class="dropdown-menu">
                        @can('create_purchase')
                            <a class="dropdown-item {{ request()->routeIs('purchases.create') ? 'active' : '' }}" href="{{ route('purchases.create') }}"><i class="bi bi-journal-plus mr-2"></i>{{ __('menu.create-purchase') }}</a>
                        @endcan
                        <a class="dropdown-item {{ request()->routeIs('purchases.index') ? 'active' : '' }}" href="{{ route('purchases.index') }}"><i class="bi bi-journals mr-2"></i>{{ __('menu.all-purchases') }}</a>
                    </div>
                </li>
                @endcan

                @can('access_purchase_returns')
                <li class="app-topnav-item dropdown {{ request()->routeIs('purchase-returns.*') || request()->routeIs('purchase-return-payments.*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-arrow-return-right"></i> <span>{{ __('menu.purchase-returns') }}</span>
                    </a>
                    <div class="dropdown-menu">
                        @can('create_purchase_returns')
                            <a class="dropdown-item {{ request()->routeIs('purchase-returns.create') ? 'active' : '' }}" href="{{ route('purchase-returns.create') }}"><i class="bi bi-journal-plus mr-2"></i>{{ __('menu.create-purchase-return') }}</a>
                        @endcan
                        <a class="dropdown-item {{ request()->routeIs('purchase-returns.index') ? 'active' : '' }}" href="{{ route('purchase-returns.index') }}"><i class="bi bi-journals mr-2"></i>{{ __('menu.all-purchase-returns') }}</a>
                    </div>
                </li>
                @endcan

                @can('access_sales')
                <li class="app-topnav-item dropdown {{ request()->routeIs('sales.*') || request()->routeIs('sale-payments*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-receipt"></i> <span>{{ __('menu.sales') }}</span>
                    </a>
                    <div class="dropdown-menu">
                        @can('create_sales')
                            <a class="dropdown-item {{ request()->routeIs('sales.create') ? 'active' : '' }}" href="{{ route('sales.create') }}"><i class="bi bi-journal-plus mr-2"></i>{{ __('menu.create-sale') }}</a>
                        @endcan
                        <a class="dropdown-item {{ request()->routeIs('sales.index') ? 'active' : '' }}" href="{{ route('sales.index') }}"><i class="bi bi-journals mr-2"></i>{{ __('menu.all-sales') }}</a>
                    </div>
                </li>
                @endcan

                @can('access_sale_returns')
                <li class="app-topnav-item dropdown {{ request()->routeIs('sale-returns.*') || request()->routeIs('sale-return-payments.*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-arrow-return-left"></i> <span>{{ __('menu.sale-returns') }}</span>
                    </a>
                    <div class="dropdown-menu">
                        @can('create_sale_returns')
                            <a class="dropdown-item {{ request()->routeIs('sale-returns.create') ? 'active' : '' }}" href="{{ route('sale-returns.create') }}"><i class="bi bi-journal-plus mr-2"></i>{{ __('menu.create-sale-return') }}</a>
                        @endcan
                        <a class="dropdown-item {{ request()->routeIs('sale-returns.index') ? 'active' : '' }}" href="{{ route('sale-returns.index') }}"><i class="bi bi-journals mr-2"></i>{{ __('menu.all-sale-returns') }}</a>
                    </div>
                </li>
                @endcan

                @can('access_expenses')
                <li class="app-topnav-item dropdown {{ request()->routeIs('expenses.*') || request()->routeIs('expense-categories.*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-wallet2"></i> <span>{{ __('menu.expenses') }}</span>
                    </a>
                    <div class="dropdown-menu">
                        @can('access_expense_categories')
                            <a class="dropdown-item {{ request()->routeIs('expense-categories.*') ? 'active' : '' }}" href="{{ route('expense-categories.index') }}"><i class="bi bi-collection mr-2"></i>{{ __('menu.categories') }}</a>
                        @endcan
                        @can('create_expenses')
                            <a class="dropdown-item {{ request()->routeIs('expenses.create') ? 'active' : '' }}" href="{{ route('expenses.create') }}"><i class="bi bi-journal-plus mr-2"></i>{{ __('menu.create-expense') }}</a>
                        @endcan
                        <a class="dropdown-item {{ request()->routeIs('expenses.index') ? 'active' : '' }}" href="{{ route('expenses.index') }}"><i class="bi bi-journals mr-2"></i>{{ __('menu.all-expenses') }}</a>
                    </div>
                </li>
                @endcan

                @can('access_customers|access_suppliers')
                <li class="app-topnav-item dropdown {{ request()->routeIs('customers.*') || request()->routeIs('suppliers.*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-people"></i> <span>{{ __('menu.parties') }}</span>
                    </a>
                    <div class="dropdown-menu">
                        @can('access_customers')
                            <a class="dropdown-item {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}"><i class="bi bi-people-fill mr-2"></i>{{ __('menu.customers') }}</a>
                        @endcan
                        @can('access_suppliers')
                            <a class="dropdown-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}"><i class="bi bi-people-fill mr-2"></i>{{ __('menu.suppliers') }}</a>
                        @endcan
                    </div>
                </li>
                @endcan

                @can('access_reports')
                <li class="app-topnav-item dropdown {{ request()->routeIs('*-report.index') ? 'is-active' : '' }}">
                    <a class="app-topnav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-graph-up"></i> <span>{{ __('menu.reports') }}</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item {{ request()->routeIs('profit-loss-report.index') ? 'active' : '' }}" href="{{ route('profit-loss-report.index') }}"><i class="bi bi-clipboard-data mr-2"></i>{{ __('menu.profit-loss-report') }}</a>
                        <a class="dropdown-item {{ request()->routeIs('payments-report.index') ? 'active' : '' }}" href="{{ route('payments-report.index') }}"><i class="bi bi-clipboard-data mr-2"></i>{{ __('menu.payments-report') }}</a>
                        <a class="dropdown-item {{ request()->routeIs('sales-report.index') ? 'active' : '' }}" href="{{ route('sales-report.index') }}"><i class="bi bi-clipboard-data mr-2"></i>{{ __('menu.sales-report') }}</a>
                        <a class="dropdown-item {{ request()->routeIs('purchases-report.index') ? 'active' : '' }}" href="{{ route('purchases-report.index') }}"><i class="bi bi-clipboard-data mr-2"></i>{{ __('menu.purchases-report') }}</a>
                        <a class="dropdown-item {{ request()->routeIs('sales-return-report.index') ? 'active' : '' }}" href="{{ route('sales-return-report.index') }}"><i class="bi bi-clipboard-data mr-2"></i>{{ __('menu.sales-return-report') }}</a>
                        <a class="dropdown-item {{ request()->routeIs('purchases-return-report.index') ? 'active' : '' }}" href="{{ route('purchases-return-report.index') }}"><i class="bi bi-clipboard-data mr-2"></i>{{ __('menu.purchases-return-report') }}</a>
                        <a class="dropdown-item {{ request()->routeIs('inventory-valuation-report.index') ? 'active' : '' }}" href="{{ route('inventory-valuation-report.index') }}"><i class="bi bi-box-seam mr-2"></i>{{ __('menu.inventory-valuation-report') }}</a>
                        <a class="dropdown-item {{ request()->routeIs('low-stock-report.index') ? 'active' : '' }}" href="{{ route('low-stock-report.index') }}"><i class="bi bi-exclamation-triangle mr-2"></i>{{ __('menu.low-stock-report') }}</a>
                        <a class="dropdown-item {{ request()->routeIs('stock-movement-report.index') ? 'active' : '' }}" href="{{ route('stock-movement-report.index') }}"><i class="bi bi-arrow-left-right mr-2"></i>{{ __('menu.stock-movement-report') }}</a>
                        <a class="dropdown-item {{ request()->routeIs('product-movement-report.index') ? 'active' : '' }}" href="{{ route('product-movement-report.index') }}"><i class="bi bi-speedometer2 mr-2"></i>{{ __('menu.product-movement-report') }}</a>
                    </div>
                </li>
                @endcan

                @can('access_user_management')
                <li class="app-topnav-item dropdown {{ request()->routeIs('users*') || request()->routeIs('roles*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-people"></i> <span>{{ __('menu.user-management') }}</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item {{ request()->routeIs('users.create') ? 'active' : '' }}" href="{{ route('users.create') }}"><i class="bi bi-person-plus mr-2"></i>{{ __('menu.create-user') }}</a>
                        <a class="dropdown-item {{ request()->routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-person-lines-fill mr-2"></i>{{ __('menu.all-users') }}</a>
                        <a class="dropdown-item {{ request()->routeIs('roles*') ? 'active' : '' }}" href="{{ route('roles.index') }}"><i class="bi bi-key mr-2"></i>{{ __('menu.roles-permissions') }}</a>
                    </div>
                </li>
                @endcan

                @can('access_activity_logs')
                <li class="app-topnav-item {{ request()->routeIs('activity-logs.*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link" href="{{ route('activity-logs.index') }}">
                        <i class="bi bi-clock-history"></i> <span>{{ __('menu.activity-logs') }}</span>
                    </a>
                </li>
                @endcan

                @can('access_currencies|access_settings')
                <li class="app-topnav-item dropdown {{ request()->routeIs('currencies*') || request()->routeIs('units*') || request()->routeIs('settings*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-gear"></i> <span>{{ __('menu.settings') }}</span>
                    </a>
                    <div class="dropdown-menu">
                        @can('access_units')
                            <a class="dropdown-item {{ request()->routeIs('units*') ? 'active' : '' }}" href="{{ route('units.index') }}"><i class="bi bi-calculator mr-2"></i>{{ __('menu.units') }}</a>
                        @endcan
                        @can('access_currencies')
                            <a class="dropdown-item {{ request()->routeIs('currencies*') ? 'active' : '' }}" href="{{ route('currencies.index') }}"><i class="bi bi-cash-stack mr-2"></i>{{ __('menu.currencies') }}</a>
                        @endcan
                        @can('access_settings')
                            <a class="dropdown-item {{ request()->routeIs('settings*') ? 'active' : '' }}" href="{{ route('settings.index') }}"><i class="bi bi-sliders mr-2"></i>{{ __('menu.system-settings') }}</a>
                        @endcan
                    </div>
                </li>
                @endcan

                <li class="app-topnav-item {{ request()->routeIs('documentation.*') ? 'is-active' : '' }}">
                    <a class="app-topnav-link" href="{{ route('documentation.index') }}">
                        <i class="bi bi-book"></i> <span>{{ __('menu.documentation') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
