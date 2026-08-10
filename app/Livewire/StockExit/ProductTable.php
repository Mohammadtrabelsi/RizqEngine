<?php

namespace App\Livewire\StockExit;

use Livewire\Component;

/**
 * Collects the products (and out quantities) for a new Bon de Sortie.
 * Products are pushed in by the shared <livewire:search-product/> component.
 */
class ProductTable extends Component
{
    protected $listeners = ['productSelected'];

    public $products = [];

    public function mount(): void
    {
        $this->products = [];
    }

    public function render()
    {
        return view('livewire.stockexit.product-table');
    }

    public function productSelected($product): void
    {
        if (in_array($product, $this->products)) {
            session()->flash('error', trans('product.product-already-added-to-cart'));

            return;
        }

        $this->products[] = $product;
    }

    public function removeProduct($key): void
    {
        unset($this->products[$key]);
    }
}
