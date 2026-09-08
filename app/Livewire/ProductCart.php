<?php

namespace App\Livewire;

use App\Models\Tax;
use App\Services\CartPricingService;
use App\Services\ProductCatalogService;
use App\Services\StockService;
use App\Services\WithholdingTaxCalculator;
use App\Services\WithholdingTaxService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Component;

class ProductCart extends Component
{
    public $listeners = ['productSelected', 'discountModalRefresh'];

    public $cart_instance;

    public $global_discount;

    public $global_tax;

    /**
     * Tax entry mode for the document. When 'included' ("Taxe incluse") no
     * tax is added on top; when 'excluded' ("Hors taxes") the user may pick
     * zero, one or many taxes from the taxes master data.
     */
    public string $tax_mode = 'included';

    /** @var array<int, int> IDs of the taxes selected in "Hors taxes" mode. */
    public array $selected_taxes = [];

    /**
     * @var array<int, int> IDs of the withholding taxes (retenues à la source)
     *                      applied to this document.
     */
    public array $selected_withholding_taxes = [];

    public $shipping;

    public $quantity;

    public $check_quantity;

    public $discount_type;

    public $item_discount;

    public $unit_price;

    public $data;

    public function mount($cartInstance, $data = null)
    {
        $this->cart_instance = $cartInstance;

        if ($data) {
            $this->data = $data;

            $this->global_discount = $data->discount_percentage;
            $this->global_tax = $data->tax_percentage;
            $this->shipping = $data->shipping_amount;

            // Existing documents only persist the resulting tax percentage, not
            // which taxes were combined, so reflect that as "Hors taxes" with a
            // non-zero rate (or "Taxe incluse" when there is no tax).
            $this->tax_mode = $data->tax_percentage > 0 ? 'excluded' : 'included';

            $this->updatedGlobalTax();
            $this->updatedGlobalDiscount();

            // Restore the withholding taxes previously applied to the document.
            if (method_exists($data, 'withholdingTaxes')) {
                $this->selected_withholding_taxes = $data->withholdingTaxes()
                    ->whereNotNull('withholding_tax_id')
                    ->pluck('withholding_tax_id')
                    ->map(fn ($id) => (int) $id)
                    ->all();
            }

            $cart_items = Cart::instance($this->cart_instance)->content();

            foreach ($cart_items as $cart_item) {
                $this->check_quantity[$cart_item->id] = [$cart_item->options['stock']];
                $this->quantity[$cart_item->id] = $cart_item->qty;
                $this->unit_price[$cart_item->id] = $cart_item->price;
                $this->discount_type[$cart_item->id] = $cart_item->options['product_discount_type'];
                if ($cart_item->options['product_discount_type'] == 'fixed') {
                    $this->item_discount[$cart_item->id] = $cart_item->options['product_discount'];
                } elseif ($cart_item->options['product_discount_type'] == 'percentage') {
                    $this->item_discount[$cart_item->id] = round(100 * ($cart_item->options['product_discount'] / $cart_item->price));
                }
            }
        } else {
            $this->global_discount = 0;
            $this->global_tax = 0;
            $this->tax_mode = 'included';
            $this->selected_taxes = [];
            $this->selected_withholding_taxes = [];
            $this->shipping = 0.00;
            $this->check_quantity = [];
            $this->quantity = [];
            $this->unit_price = [];
            $this->discount_type = [];
            $this->item_discount = [];
        }
    }

    public function render()
    {
        $cart_items = Cart::instance($this->cart_instance)->content();

        $ttc = (float) Cart::instance($this->cart_instance)->total() + (float) $this->shipping;
        $tva = (float) Cart::instance($this->cart_instance)->tax();

        $side = in_array($this->cart_instance, Tax::SALE_CART_INSTANCES, true) ? 'sale' : 'purchase';

        $available_withholding_taxes = app(WithholdingTaxService::class)->selectableFor($side);

        $withholding = app(WithholdingTaxCalculator::class)->calculate(
            $available_withholding_taxes->whereIn('id', $this->selected_withholding_taxes),
            $ttc - $tva,
            $tva,
            $ttc,
        );

        return view('livewire.product-cart', [
            'cart_items' => $cart_items,
            'total_with_shipping' => $ttc,
            'available_taxes' => Tax::forCartInstance($this->cart_instance)
                ->orderBy('order')
                ->orderBy('name')
                ->get(),
            'available_withholding_taxes' => $available_withholding_taxes,
            'withholding' => $withholding,
        ]);
    }

    public function updatedSelectedWithholdingTaxes(): void
    {
        // Reactive recompute happens in render(); this hook keeps Livewire
        // aware of the change so the net-payable preview refreshes live.
    }

    public function updatedTaxMode(): void
    {
        if ($this->tax_mode === 'included') {
            $this->selected_taxes = [];
        }

        $this->recalculateGlobalTax();
    }

    public function updatedSelectedTaxes(): void
    {
        $this->recalculateGlobalTax();
    }

    /**
     * Derive the single global tax percentage the cart works with from the
     * chosen tax mode and the selected taxes. In "Taxe incluse" mode no tax is
     * added; in "Hors taxes" mode the selected percentage taxes are compounded
     * one after another (19% then 7% is 27.33%, not 26%), not summed.
     * Fixed-amount taxes are recorded on the document but, since the cart
     * only tracks one percentage figure today, are not yet folded into it.
     */
    public function recalculateGlobalTax(): void
    {
        if ($this->tax_mode === 'included' || empty($this->selected_taxes)) {
            $this->global_tax = 0;
        } else {
            $this->global_tax = Tax::compoundPercentageRate($this->selected_taxes);
        }

        $this->updatedGlobalTax();
    }

    public function productSelected($product)
    {
        $cart = Cart::instance($this->cart_instance);

        $exists = $cart->search(function ($cartItem, $rowId) use ($product) {
            return $cartItem->id == $product['id'];
        });

        if ($exists->isNotEmpty()) {
            session()->flash('error', trans('product.product-already-added-to-cart'));

            return;
        }

        $cart->add([
            'id' => $product['id'],
            'name' => translatable_string($product['product_name']),
            'qty' => 1,
            'price' => $this->calculate($product)['price'],
            'weight' => 1,
            'options' => [
                'product_discount' => 0.00,
                'product_discount_type' => 'fixed',
                'sub_total' => $this->calculate($product)['sub_total'],
                'code' => $product['product_code'],
                'stock' => $product['product_quantity'],
                'unit' => $product['product_unit'],
                'product_tax' => $this->calculate($product)['product_tax'],
                'unit_price' => $this->calculate($product)['unit_price'],
            ],
        ]);

        $this->check_quantity[$product['id']] = max(0, $product['product_quantity'] - StockService::MINIMUM_STOCK);
        $this->quantity[$product['id']] = 1;
        $this->discount_type[$product['id']] = 'fixed';
        $this->item_discount[$product['id']] = 0;
    }

    public function removeItem($row_id)
    {
        Cart::instance($this->cart_instance)->remove($row_id);
    }

    public function updatedGlobalTax()
    {
        Cart::instance($this->cart_instance)->setGlobalTax((float) $this->global_tax);
    }

    public function updatedGlobalDiscount()
    {
        Cart::instance($this->cart_instance)->setGlobalDiscount((int) $this->global_discount);
    }

    public function updateQuantity($row_id, $product_id)
    {
        if ($this->cart_instance == 'sale' || $this->cart_instance == 'purchase_return') {
            if ($this->check_quantity[$product_id] < $this->quantity[$product_id]) {
                session()->flash('error', trans('product.requested-quantity-not-available'));

                return;
            }
        }

        Cart::instance($this->cart_instance)->update($row_id, $this->quantity[$product_id]);

        $cart_item = Cart::instance($this->cart_instance)->get($row_id);

        Cart::instance($this->cart_instance)->update($row_id, [
            'options' => [
                'sub_total' => $cart_item->price * $cart_item->qty,
                'code' => $cart_item->options['code'],
                'stock' => $cart_item->options['stock'],
                'unit' => $cart_item->options['unit'],
                'product_tax' => $cart_item->options['product_tax'],
                'unit_price' => $cart_item->options['unit_price'],
                'product_discount' => $cart_item->options['product_discount'],
                'product_discount_type' => $cart_item->options['product_discount_type'],
            ],
        ]);
    }

    public function updatedDiscountType($value, $name)
    {
        $this->item_discount[$name] = 0;
    }

    public function discountModalRefresh($product_id, $row_id)
    {
        $this->updateQuantity($row_id, $product_id);
    }

    public function setProductDiscount($row_id, $product_id)
    {
        $cart_item = Cart::instance($this->cart_instance)->get($row_id);

        if ($this->discount_type[$product_id] == 'fixed') {
            Cart::instance($this->cart_instance)
                ->update($row_id, [
                    'price' => ($cart_item->price + $cart_item->options['product_discount']) - $this->item_discount[$product_id],
                ]);

            $discount_amount = $this->item_discount[$product_id];

            $this->updateCartOptions($row_id, $product_id, $cart_item, $discount_amount);
        } elseif ($this->discount_type[$product_id] == 'percentage') {
            $discount_amount = ($cart_item->price + $cart_item->options['product_discount']) * ($this->item_discount[$product_id] / 100);

            Cart::instance($this->cart_instance)
                ->update($row_id, [
                    'price' => ($cart_item->price + $cart_item->options['product_discount']) - $discount_amount,
                ]);

            $this->updateCartOptions($row_id, $product_id, $cart_item, $discount_amount);
        }

        session()->flash('success', trans('product.discount-added-to-product'));
    }

    public function updatePrice($row_id, $product_id)
    {
        $product = app(ProductCatalogService::class)->findOrFail($product_id);

        $cart_item = Cart::instance($this->cart_instance)->get($row_id);

        Cart::instance($this->cart_instance)->update($row_id, ['price' => $this->unit_price[$product['id']]]);

        Cart::instance($this->cart_instance)->update($row_id, [
            'options' => [
                'sub_total' => $this->calculate($product, $this->unit_price[$product['id']])['sub_total'],
                'code' => $cart_item->options['code'],
                'stock' => $cart_item->options['stock'],
                'unit' => $cart_item->options['unit'],
                'product_tax' => $this->calculate($product, $this->unit_price[$product['id']])['product_tax'],
                'unit_price' => $this->calculate($product, $this->unit_price[$product['id']])['unit_price'],
                'product_discount' => $cart_item->options['product_discount'],
                'product_discount_type' => $cart_item->options['product_discount_type'],
            ],
        ]);
    }

    public function calculate($product, $new_price = null)
    {
        if ($new_price) {
            $product_price = $new_price;
        } else {
            $this->unit_price[$product['id']] = $product['product_price'];
            if ($this->cart_instance == 'purchase' || $this->cart_instance == 'purchase_return') {
                $this->unit_price[$product['id']] = $product['product_cost'];
            }
            $product_price = $this->unit_price[$product['id']];
        }

        return app(CartPricingService::class)->calculate($product, $product_price);
    }

    public function updateCartOptions($row_id, $product_id, $cart_item, $discount_amount)
    {
        Cart::instance($this->cart_instance)->update($row_id, ['options' => [
            'sub_total' => $cart_item->price * $cart_item->qty,
            'code' => $cart_item->options['code'],
            'stock' => $cart_item->options['stock'],
            'unit' => $cart_item->options['unit'],
            'product_tax' => $cart_item->options['product_tax'],
            'unit_price' => $cart_item->options['unit_price'],
            'product_discount' => $discount_amount,
            'product_discount_type' => $this->discount_type[$product_id],
        ]]);
    }
}
