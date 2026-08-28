<?php

namespace App\Livewire;

use Livewire\Component;

class Landing extends Component
{
    /**
     * Marketing landing page. Static content driven by lang files so the
     * feature grid and hero copy translate between EN/FR.
     *
     * @return array<int, array{index:string, title:string, body:string, anchor?:string}>
     */
    public function modules(): array
    {
        return [
            ['index' => '01', 'title' => __('pos.landing.modules.products.title'),  'body' => __('pos.landing.modules.products.body')],
            ['index' => '02', 'title' => __('pos.landing.modules.sales.title'),     'body' => __('pos.landing.modules.sales.body')],
            ['index' => '03', 'title' => __('pos.landing.modules.purchases.title'), 'body' => __('pos.landing.modules.purchases.body')],
            ['index' => '04', 'title' => __('pos.landing.modules.roles.title'),     'body' => __('pos.landing.modules.roles.body'), 'anchor' => 'access'],
            ['index' => '05', 'title' => __('pos.landing.modules.reporting.title'), 'body' => __('pos.landing.modules.reporting.body'), 'anchor' => 'reporting'],
            ['index' => '06', 'title' => __('pos.landing.modules.expenses.title'),  'body' => __('pos.landing.modules.expenses.body')],
        ];
    }

    public function render()
    {
        return view('livewire.landing')->layout('layouts.guest');
    }
}
