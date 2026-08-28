<?php
// app/Livewire/LandingPage.php

namespace App\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Component;

class LandingPage extends Component
{
    #[Computed] public function stats(): array
    {
        return [
            ['value' => '1 002',  'label' => __('landing.stat.skus')],
            ['value' => '<150ms', 'label' => __('landing.stat.till')],
            ['value' => '14',     'label' => __('landing.stat.roles')],
        ];
    }

    #[Computed] public function features(): array
    {
        return [
            ['title' => __('feature.products.title'),  'body' => __('feature.products.body')],
            ['title' => __('feature.sales.title'),     'body' => __('feature.sales.body')],
            ['title' => __('feature.purchases.title'), 'body' => __('feature.purchases.body')],
            ['title' => __('feature.roles.title'),     'body' => __('feature.roles.body'), 'anchor' => 'access'],
            ['title' => __('feature.reporting.title'), 'body' => __('feature.reporting.body'), 'anchor' => 'reporting'],
            ['title' => __('feature.expenses.title'),  'body' => __('feature.expenses.body')],
        ];
    }

    /** Static hero illustration data — not real cart state. */
    #[Computed] public function mockCart(): array
    {
        return [
            ['name' => 'Espresso beans 1kg',   'qty' => 2, 'price' => 42.00, 'highlight' => false],
            ['name' => 'Paper cups 8oz (50)',  'qty' => 4, 'price' => 12.15, 'highlight' => false],
            ['name' => 'Filter papers V60',    'qty' => 1, 'price' => 51.90, 'highlight' => true],
        ];
    }

    public function render()
    {
        return view('livewire.landing-page')->layout('layouts.app');
    }
}
