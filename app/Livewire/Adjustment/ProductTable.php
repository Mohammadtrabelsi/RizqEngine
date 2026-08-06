<?php

namespace App\Livewire\Adjustment;

use Livewire\Component;

class ProductTable extends Component
{
    protected $listeners = ['productSelected'];

    public $products;

    public $hasAdjustments;

    public function mount($adjustedProducts = null)
    {
        $this->products = [];

        if ($adjustedProducts) {
            $this->hasAdjustments = true;
            $this->products = $adjustedProducts;
        } else {
            $this->hasAdjustments = false;
        }
    }

    public function render()
    {
        return view('livewire.adjustment.product-table');
    }

    public function productSelected($product)
    {
        switch ($this->hasAdjustments) {
            case true:
                if (in_array($product, array_map(function ($adjustment) {
                    return $adjustment['product'];
                }, $this->products))) {
                    session()->flash('error', trans('product.product-already-added-to-cart'));

                    return;
                }
                break;
            case false:
                if (in_array($product, $this->products)) {
                    session()->flash('error', trans('product.product-already-added-to-cart'));

                    return;
                }
                break;
            default:
                session()->flash('error', trans('product.something-went-wrong'));

                return;
        }

        array_push($this->products, $product);
    }

    public function removeProduct($key)
    {
        unset($this->products[$key]);
    }
}
