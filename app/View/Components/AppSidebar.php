<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Primary application sidebar. The nav group/item definitions live here so the
 * template carries no @php. Items whose route is not registered are skipped by
 * the template, so the sidebar never links to a 404.
 */
class AppSidebar extends Component
{
    /** @var array<string, list<array<string, mixed>>> */
    public array $groups;

    public function __construct(
        public string $active = 'dashboard',
        public int $orderCount = 0,
    ) {
        $this->groups = [
            'overview' => [
                ['key' => 'dashboard', 'label' => __('nav.dashboard'), 'route' => 'dashboard', 'dot' => true],
                ['key' => 'pos', 'label' => __('nav.point_of_sale'), 'route' => 'app.pos.index', 'dot' => true],
            ],
            'transactions' => [
                ['key' => 'products', 'label' => __('nav.products'), 'route' => 'products.index'],
                ['key' => 'transactions', 'label' => __('nav.transactions'), 'route' => 'sales.index'],
                ['key' => 'quotes', 'label' => __('nav.quotes'), 'route' => 'quotations.index'],
                ['key' => 'orders', 'label' => __('nav.orders'), 'route' => 'commandes.index', 'badge' => $orderCount],
                ['key' => 'expenses', 'label' => __('nav.expenses'), 'route' => 'expenses.index'],
            ],
            'people' => [
                ['key' => 'customers', 'label' => __('nav.customers'), 'route' => 'customers.index'],
                ['key' => 'suppliers', 'label' => __('nav.suppliers'), 'route' => 'suppliers.index'],
                ['key' => 'staff', 'label' => __('nav.staff_roles'), 'route' => 'users.index'],
            ],
            'insight' => [
                ['key' => 'reports', 'label' => __('nav.reports'), 'route' => 'sales-report.index'],
                ['key' => 'settings', 'label' => __('nav.settings'), 'route' => 'settings.index'],
            ],
        ];
    }

    public function render(): View
    {
        return view('components.app-sidebar');
    }
}
