{{--
    RizqEngine — secondary (inline) navigation bar.
    Renders the items of the currently-active primary section inline,
    replacing the old hover dropdowns. Shown only for sections that have
    more than a single destination.
--}}

@if ($hasSecondary)
<nav class="app-subnav" aria-label="{{ __('menu.products') }}">
    <div class="container-fluid">
        <ul class="app-subnav-list">

            @if ($inProducts)
                @can('access_product_categories')
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('product-categories.*') ? 'is-active' : '' }}" href="{{ route('product-categories.index') }}"><i class="bi bi-collection"></i> <span>{{ __('menu.categories') }}</span></a></li>
                @endcan
                @can('create_products')
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('products.create') ? 'is-active' : '' }}" href="{{ route('products.create') }}"><i class="bi bi-journal-plus"></i> <span>{{ __('menu.create-product') }}</span></a></li>
                @endcan
                <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('products.index') ? 'is-active' : '' }}" href="{{ route('products.index') }}"><i class="bi bi-journals"></i> <span>{{ __('menu.all-products') }}</span></a></li>
                @can('print_barcodes')
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('barcode.print') ? 'is-active' : '' }}" href="{{ route('barcode.print') }}"><i class="bi bi-printer"></i> <span>{{ __('menu.print-barcode') }}</span></a></li>
                @endcan
            @endif

            @if ($inSales)
                @can('access_sales')
                    @can('create_sales')
                        <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('sales.create') ? 'is-active' : '' }}" href="{{ route('sales.create') }}"><i class="bi bi-journal-plus"></i> <span>{{ __('menu.create-sale') }}</span></a></li>
                    @endcan
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('sales.index') ? 'is-active' : '' }}" href="{{ route('sales.index') }}"><i class="bi bi-receipt"></i> <span>{{ __('menu.all-sales') }}</span></a></li>
                @endcan
                @can('access_sale_returns')
                    @can('create_sale_returns')
                        <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('sale-returns.create') ? 'is-active' : '' }}" href="{{ route('sale-returns.create') }}"><i class="bi bi-journal-plus"></i> <span>{{ __('menu.create-sale-return') }}</span></a></li>
                    @endcan
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('sale-returns.index') ? 'is-active' : '' }}" href="{{ route('sale-returns.index') }}"><i class="bi bi-arrow-return-left"></i> <span>{{ __('menu.all-sale-returns') }}</span></a></li>
                @endcan
            @endif

            @if ($inPurchases)
                @can('access_purchases')
                    @can('create_purchase')
                        <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('purchases.create') ? 'is-active' : '' }}" href="{{ route('purchases.create') }}"><i class="bi bi-journal-plus"></i> <span>{{ __('menu.create-purchase') }}</span></a></li>
                    @endcan
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('purchases.index') ? 'is-active' : '' }}" href="{{ route('purchases.index') }}"><i class="bi bi-bag"></i> <span>{{ __('menu.all-purchases') }}</span></a></li>
                @endcan
                @can('access_purchase_returns')
                    @can('create_purchase_returns')
                        <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('purchase-returns.create') ? 'is-active' : '' }}" href="{{ route('purchase-returns.create') }}"><i class="bi bi-journal-plus"></i> <span>{{ __('menu.create-purchase-return') }}</span></a></li>
                    @endcan
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('purchase-returns.index') ? 'is-active' : '' }}" href="{{ route('purchase-returns.index') }}"><i class="bi bi-arrow-return-right"></i> <span>{{ __('menu.all-purchase-returns') }}</span></a></li>
                @endcan
            @endif

            @if ($inStock)
                @canany(['access_stock_exits', 'create_stock_exits'])
                    @can('create_stock_exits')
                        <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('stock-exits.create') ? 'is-active' : '' }}" href="{{ route('stock-exits.create') }}"><i class="bi bi-journal-plus"></i> <span>{{ __('menu.create-stock-exit') }}</span></a></li>
                    @endcan
                    @can('access_stock_exits')
                        <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('stock-exits.index') ? 'is-active' : '' }}" href="{{ route('stock-exits.index') }}"><i class="bi bi-box-arrow-up"></i> <span>{{ __('menu.all-stock-exits') }}</span></a></li>
                    @endcan
                @endcanany
                @can('access_adjustments')
                    @can('create_adjustments')
                        <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('adjustments.create') ? 'is-active' : '' }}" href="{{ route('adjustments.create') }}"><i class="bi bi-journal-plus"></i> <span>{{ __('menu.create-adjustment') }}</span></a></li>
                    @endcan
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('adjustments.index') ? 'is-active' : '' }}" href="{{ route('adjustments.index') }}"><i class="bi bi-clipboard-check"></i> <span>{{ __('menu.all-adjustments') }}</span></a></li>
                @endcan
            @endif

            @if ($inQuotations)
                @can('create_adjustments')
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('quotations.create') ? 'is-active' : '' }}" href="{{ route('quotations.create') }}"><i class="bi bi-journal-plus"></i> <span>{{ __('menu.create-quotation') }}</span></a></li>
                @endcan
                <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('quotations.index') ? 'is-active' : '' }}" href="{{ route('quotations.index') }}"><i class="bi bi-journals"></i> <span>{{ __('menu.all-quotations') }}</span></a></li>
            @endif

            @if ($inOrders)
                @can('access_bon_commandes')
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('bon-commandes.*') ? 'is-active' : '' }}" href="{{ route('bon-commandes.index') }}"><i class="bi bi-clipboard-check"></i> <span>{{ __('menu.all-bon-commandes') }}</span></a></li>
                @endcan
                @can('access_commandes')
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('commandes.*') ? 'is-active' : '' }}" href="{{ route('commandes.index') }}"><i class="bi bi-bag-check"></i> <span>{{ __('menu.all-commandes') }}</span></a></li>
                @endcan
            @endif

            @if ($inExpenses)
                @can('access_expense_categories')
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('expense-categories.*') ? 'is-active' : '' }}" href="{{ route('expense-categories.index') }}"><i class="bi bi-collection"></i> <span>{{ __('menu.categories') }}</span></a></li>
                @endcan
                @can('create_expenses')
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('expenses.create') ? 'is-active' : '' }}" href="{{ route('expenses.create') }}"><i class="bi bi-journal-plus"></i> <span>{{ __('menu.create-expense') }}</span></a></li>
                @endcan
                <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('expenses.index') ? 'is-active' : '' }}" href="{{ route('expenses.index') }}"><i class="bi bi-journals"></i> <span>{{ __('menu.all-expenses') }}</span></a></li>
            @endif

            @if ($inParties)
                @can('access_customers')
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('customers.*') ? 'is-active' : '' }}" href="{{ route('customers.index') }}"><i class="bi bi-people-fill"></i> <span>{{ __('menu.customers') }}</span></a></li>
                @endcan
                @can('access_suppliers')
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('suppliers.*') ? 'is-active' : '' }}" href="{{ route('suppliers.index') }}"><i class="bi bi-people-fill"></i> <span>{{ __('menu.suppliers') }}</span></a></li>
                @endcan
            @endif

            @if ($inReports)
                <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('profit-loss-report.index') ? 'is-active' : '' }}" href="{{ route('profit-loss-report.index') }}"><i class="bi bi-clipboard-data"></i> <span>{{ __('menu.profit-loss-report') }}</span></a></li>
                <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('payments-report.index') ? 'is-active' : '' }}" href="{{ route('payments-report.index') }}"><i class="bi bi-clipboard-data"></i> <span>{{ __('menu.payments-report') }}</span></a></li>
                <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('sales-report.index') ? 'is-active' : '' }}" href="{{ route('sales-report.index') }}"><i class="bi bi-clipboard-data"></i> <span>{{ __('menu.sales-report') }}</span></a></li>
                <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('purchases-report.index') ? 'is-active' : '' }}" href="{{ route('purchases-report.index') }}"><i class="bi bi-clipboard-data"></i> <span>{{ __('menu.purchases-report') }}</span></a></li>
                <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('sales-return-report.index') ? 'is-active' : '' }}" href="{{ route('sales-return-report.index') }}"><i class="bi bi-clipboard-data"></i> <span>{{ __('menu.sales-return-report') }}</span></a></li>
                <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('purchases-return-report.index') ? 'is-active' : '' }}" href="{{ route('purchases-return-report.index') }}"><i class="bi bi-clipboard-data"></i> <span>{{ __('menu.purchases-return-report') }}</span></a></li>
                <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('inventory-valuation-report.index') ? 'is-active' : '' }}" href="{{ route('inventory-valuation-report.index') }}"><i class="bi bi-box-seam"></i> <span>{{ __('menu.inventory-valuation-report') }}</span></a></li>
                <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('low-stock-report.index') ? 'is-active' : '' }}" href="{{ route('low-stock-report.index') }}"><i class="bi bi-exclamation-triangle"></i> <span>{{ __('menu.low-stock-report') }}</span></a></li>
                <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('stock-movement-report.index') ? 'is-active' : '' }}" href="{{ route('stock-movement-report.index') }}"><i class="bi bi-arrow-left-right"></i> <span>{{ __('menu.stock-movement-report') }}</span></a></li>
                <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('product-movement-report.index') ? 'is-active' : '' }}" href="{{ route('product-movement-report.index') }}"><i class="bi bi-speedometer2"></i> <span>{{ __('menu.product-movement-report') }}</span></a></li>
            @endif

            @if ($inUsers)
                <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('users.create') ? 'is-active' : '' }}" href="{{ route('users.create') }}"><i class="bi bi-person-plus"></i> <span>{{ __('menu.create-user') }}</span></a></li>
                <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('users.index') ? 'is-active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-person-lines-fill"></i> <span>{{ __('menu.all-users') }}</span></a></li>
                <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('roles*') ? 'is-active' : '' }}" href="{{ route('roles.index') }}"><i class="bi bi-key"></i> <span>{{ __('menu.roles-permissions') }}</span></a></li>
            @endif

            @if ($inFleet)
                @can('access_drivers')
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('drivers*') ? 'is-active' : '' }}" href="{{ route('drivers.index') }}"><i class="bi bi-person-badge"></i> <span>{{ __('menu.drivers') }}</span></a></li>
                @endcan
                @can('access_vehicles')
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('vehicles*') ? 'is-active' : '' }}" href="{{ route('vehicles.index') }}"><i class="bi bi-truck"></i> <span>{{ __('menu.vehicles') }}</span></a></li>
                @endcan
            @endif

            @if ($inSettings)
                @can('access_units')
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('units*') ? 'is-active' : '' }}" href="{{ route('units.index') }}"><i class="bi bi-calculator"></i> <span>{{ __('menu.units') }}</span></a></li>
                @endcan
                @can('access_currencies')
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('currencies*') ? 'is-active' : '' }}" href="{{ route('currencies.index') }}"><i class="bi bi-cash-stack"></i> <span>{{ __('menu.currencies') }}</span></a></li>
                @endcan
                @can('access_settings')
                    <li class="app-subnav-item"><a class="app-subnav-link {{ request()->routeIs('settings*') ? 'is-active' : '' }}" href="{{ route('settings.index') }}"><i class="bi bi-sliders"></i> <span>{{ __('menu.system-settings') }}</span></a></li>
                @endcan
            @endif

        </ul>
    </div>
</nav>
@endif
