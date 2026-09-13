<?php

namespace App\Livewire\Barcode;

use App\Models\Product;
use Livewire\Component;
use Milon\Barcode\Facades\DNS1DFacade;

class ProductTable extends Component
{
    /**
     * Products queued for barcode printing.
     *
     * Each entry is keyed by the product id and holds:
     *  - product:  the Product model
     *  - quantity: how many labels to print for that product
     */
    public $items = [];

    /** Whether the product name is printed on each label. */
    public bool $showName = true;

    /** Whether the product price is printed on each label. */
    public bool $showPrice = true;

    /** Whether the product code is printed on each label. */
    public bool $showCode = false;

    /** Generated barcode labels ready to preview / export. */
    public $labels = [];

    protected $listeners = ['productSelected'];

    public function mount()
    {
        $this->items = [];
        $this->labels = [];
    }

    public function render()
    {
        return view('livewire.barcode.product-table');
    }

    /**
     * Add a product picked from the search box to the queue.
     */
    public function productSelected(Product $product)
    {
        // Avoid duplicates, just bump the quantity instead.
        if (isset($this->items[$product->id])) {
            $this->items[$product->id]['quantity']++;

            return;
        }

        $this->items[$product->id] = [
            'product' => $product,
            'quantity' => 1,
        ];

        $this->labels = [];
    }

    /**
     * Remove a product from the queue.
     */
    public function removeItem($productId)
    {
        unset($this->items[$productId]);
        $this->labels = [];
    }

    public function clearItems()
    {
        $this->items = [];
        $this->labels = [];
    }

    /**
     * Generate the barcode labels for every queued product.
     */
    public function generateBarcodes()
    {
        if (empty($this->items)) {
            session()->flash('error', trans('product.no-products-found'));

            return;
        }

        $total = collect($this->items)->sum('quantity');

        if ($total > 100) {
            session()->flash('error', trans('product.maximum-barcode-limit'));

            return;
        }

        $this->labels = [];

        foreach ($this->items as $item) {
            $product = $item['product'];
            $quantity = max(1, (int) $item['quantity']);

            if (! is_numeric($product->product_code)) {
                session()->flash('error', trans('product.invalid-product-code').': '.$product->product_name);

                return;
            }

            $svg = DNS1DFacade::getBarCodeSVG(
                $product->product_code,
                $product->product_barcode_symbology,
                2,
                60,
                'black',
                false
            );

            for ($i = 1; $i <= $quantity; $i++) {
                $this->labels[] = [
                    'svg' => $svg,
                    'name' => $product->product_name,
                    'code' => $product->product_code,
                    'price' => $product->product_price,
                ];
            }
        }
    }

    public function getPdf()
    {
        if (empty($this->labels)) {
            session()->flash('error', trans('product.no-products-found'));

            return;
        }

        $pdf = \PDF::loadView('product.barcode.print', [
            'labels' => $this->labels,
            'showName' => $this->showName,
            'showPrice' => $this->showPrice,
            'showCode' => $this->showCode,
        ]);

        return $pdf->stream('barcodes.pdf');
    }

    /**
     * Reset the preview whenever a configuration option changes so the
     * user regenerates with the new settings.
     */
    public function updated($property)
    {
        if (in_array($property, ['showName', 'showPrice', 'showCode'], true)
            || str_starts_with($property, 'items.')) {
            $this->labels = [];
        }
    }
}
