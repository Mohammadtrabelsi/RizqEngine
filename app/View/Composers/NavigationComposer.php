<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Request;
use Illuminate\View\View;

/**
 * Supplies the primary/secondary navigation their "active section" flags and
 * the store settings, so the sidebar and secondary-menu Blade views carry no
 * @php routing logic.
 */
class NavigationComposer
{
    public function compose(View $view): void
    {
        $flags = [
            'inProducts' => Request::routeIs('products.*') || Request::routeIs('product-categories.*') || Request::routeIs('barcode.print'),
            'inTransactions' => Request::routeIs('adjustments.*') || Request::routeIs('stock-exits.*') || Request::routeIs('stock-entries.*') || Request::routeIs('purchases.*') || Request::routeIs('purchase-payments*') || Request::routeIs('purchase-returns.*') || Request::routeIs('purchase-return-payments.*') || Request::routeIs('sales.*') || Request::routeIs('sale-payments*') || Request::routeIs('sale-returns.*') || Request::routeIs('sale-return-payments.*'),
            'inQuotations' => Request::routeIs('quotations.*'),
            'inOrders' => Request::routeIs('bon-commandes.*') || Request::routeIs('commandes.*'),
            'inExpenses' => Request::routeIs('expenses.*') || Request::routeIs('expense-categories.*'),
            'inParties' => Request::routeIs('customers.*') || Request::routeIs('suppliers.*'),
            'inReports' => Request::routeIs('*-report.index'),
            'inUsers' => Request::routeIs('users*') || Request::routeIs('roles*'),
            'inSettings' => Request::routeIs('currencies*') || Request::routeIs('units*') || Request::routeIs('settings*'),
        ];

        $flags['hasSecondary'] = in_array(true, $flags, true);
        $flags['sidebarSettings'] = settings();

        $view->with($flags);
    }
}
