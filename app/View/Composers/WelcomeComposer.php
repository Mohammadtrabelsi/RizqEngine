<?php

namespace App\View\Composers;

use Illuminate\View\View;

/**
 * Supplies the marketing landing page its static feature and role lists so the
 * view carries no @php content arrays.
 */
class WelcomeComposer
{
    public function compose(View $view): void
    {
        $view->with([
            'features' => [
                ['title' => 'Products & inventory', 'desc' => 'Track stock levels, variants, barcodes and categories across every store location.'],
                ['title' => 'Purchases & suppliers', 'desc' => 'Manage supplier orders, incoming purchase batches and cost of goods seamlessly.'],
                ['title' => 'Sales & Till POS', 'desc' => 'A lightning-fast checkout counter flow optimized for high-volume store hours.'],
                ['title' => 'Returns & Refunds', 'desc' => 'Process customer returns, exchanges and store credit without breaking stock counts.'],
                ['title' => 'Expense Tracking', 'desc' => 'Log day-to-day store expenses and automatically connect them to net income.'],
                ['title' => 'People & Roles', 'desc' => 'Manage cashier, manager and owner accounts with granular permission sets.'],
            ],
            'roles' => [
                ['name' => 'Owner', 'access' => 'Full administrative access & financial reporting'],
                ['name' => 'Manager', 'access' => 'Full inventory, purchasing & sales management'],
                ['name' => 'Cashier', 'access' => 'POS checkout till & returns processing only'],
            ],
        ]);
    }
}
