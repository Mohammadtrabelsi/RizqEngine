<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Modules\Product\Entities\Product;

class HeaderComposer
{
    public function compose(View $view)
    {
        $low_quantity_products = auth()->user()->can('show_notifications')
            ? Product::select('id', 'product_quantity', 'product_stock_alert', 'product_code')
                ->whereColumn('product_quantity', '<=', 'product_stock_alert')
                ->get()
            : collect();

        $view->with('low_quantity_products', $low_quantity_products);
    }
}
