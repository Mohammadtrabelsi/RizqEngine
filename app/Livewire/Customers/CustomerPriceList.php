<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

/**
 * Manages a single customer's negotiated price list ("liste de prix par
 * client"): the per-product prices that override the default sale price on
 * that customer's sale documents.
 */
class CustomerPriceList extends Component
{
    public Customer $customer;

    /** Product selected in the "add / edit a price" form. */
    public ?int $product_id = null;

    /** Negotiated price for the selected product, in the product_price unit. */
    public $price;

    public function mount(Customer $customer): void
    {
        abort_if(Gate::denies('edit_customers'), 403);

        $this->customer = $customer;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id'),
                // One negotiated price per product, per customer. Ignore the
                // row being edited so re-saving it does not trip the rule.
                Rule::unique('customer_product_prices', 'product_id')
                    ->where('customer_id', $this->customer->id),
            ],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }

    public function save(): void
    {
        abort_if(Gate::denies('edit_customers'), 403);

        $validated = $this->validate();

        $this->customer->productPrices()->create([
            'product_id' => $validated['product_id'],
            'price' => $validated['price'],
        ]);

        $this->reset(['product_id', 'price']);

        session()->flash('success', trans('customer.price_saved'));
    }

    public function updatePrice(int $id): void
    {
        abort_if(Gate::denies('edit_customers'), 403);

        $line = $this->customer->productPrices()->findOrFail($id);

        $validated = $this->validate([
            'editing_price' => ['required', 'integer', 'min:0'],
        ], [], ['editing_price' => trans('customer.price')]);

        $line->update(['price' => $validated['editing_price']]);

        $this->cancelEdit();

        session()->flash('success', trans('customer.price_saved'));
    }

    /** Id of the price row currently being edited inline, or null. */
    public ?int $editing_id = null;

    /** Price bound to the inline edit form. */
    public $editing_price;

    public function edit(int $id): void
    {
        $line = $this->customer->productPrices()->findOrFail($id);

        $this->editing_id = $line->id;
        $this->editing_price = $line->price;
    }

    public function cancelEdit(): void
    {
        $this->reset(['editing_id', 'editing_price']);
    }

    public function delete(int $id): void
    {
        abort_if(Gate::denies('edit_customers'), 403);

        $this->customer->productPrices()->whereKey($id)->delete();

        session()->flash('warning', trans('customer.price_deleted'));
    }

    public function render()
    {
        $prices = $this->customer->productPrices()
            ->with('product')
            ->latest()
            ->get();

        // Products the customer does not yet have a negotiated price for.
        $available = Product::query()
            ->whereNotIn('id', $prices->pluck('product_id'))
            ->orderBy('product_name')
            ->get();

        return view('livewire.customers.customer-price-list', compact('prices', 'available'))
            ->layout('components.layouts.admin', ['title' => __('customer.price_list')]);
    }
}
