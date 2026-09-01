<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationDetails;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Owns querying and persistence for quotations (Devis), including building the
 * document from the "quotation" cart instance. Monetary values are stored as
 * integer cents.
 */
class QuotationService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Quotation::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('reference', 'like', $term)
                    ->orWhere('customer_name', 'like', $term);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * The customer attached to a quotation, for the detail view.
     */
    public function customerFor(Quotation $quotation): Customer
    {
        return Customer::findOrFail($quotation->customer_id);
    }

    /**
     * Create a quotation and its line items from the "quotation" cart.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Quotation
    {
        return DB::transaction(function () use ($data) {
            $cart = Cart::instance('quotation');

            $quotation = Quotation::create([
                'date' => $data['date'],
                'customer_id' => $data['customer_id'],
                'customer_name' => Customer::findOrFail($data['customer_id'])->customer_name,
                'tax_percentage' => $data['tax_percentage'],
                'discount_percentage' => $data['discount_percentage'],
                'shipping_amount' => $data['shipping_amount'] * 100,
                'total_amount' => $data['total_amount'] * 100,
                'status' => $data['status'],
                'note' => $data['note'] ?? null,
                'tax_amount' => (float) $cart->tax() * 100,
                'discount_amount' => (float) $cart->discount() * 100,
            ]);

            $this->syncDetails($quotation, $cart);

            $cart->destroy();

            return $quotation;
        });
    }

    /**
     * Replace a quotation's line items and header from the "quotation" cart.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Quotation $quotation, array $data): Quotation
    {
        return DB::transaction(function () use ($quotation, $data) {
            $cart = Cart::instance('quotation');

            foreach ($quotation->quotationDetails as $quotation_detail) {
                $quotation_detail->delete();
            }

            $quotation->update([
                'date' => $data['date'],
                'reference' => $data['reference'],
                'customer_id' => $data['customer_id'],
                'customer_name' => Customer::findOrFail($data['customer_id'])->customer_name,
                'tax_percentage' => $data['tax_percentage'],
                'discount_percentage' => $data['discount_percentage'],
                'shipping_amount' => $data['shipping_amount'] * 100,
                'total_amount' => $data['total_amount'] * 100,
                'status' => $data['status'],
                'note' => $data['note'] ?? null,
                'tax_amount' => (float) $cart->tax() * 100,
                'discount_amount' => (float) $cart->discount() * 100,
            ]);

            $this->syncDetails($quotation, $cart);

            $cart->destroy();

            return $quotation;
        });
    }

    /**
     * Reset the "quotation" cart to the given quotation's saved line items,
     * ready for editing.
     */
    public function loadCart(Quotation $quotation): void
    {
        Cart::instance('quotation')->destroy();
        $cart = Cart::instance('quotation');

        foreach ($quotation->quotationDetails as $quotation_detail) {
            $cart->add([
                'id' => $quotation_detail->product_id,
                'name' => $quotation_detail->product_name,
                'qty' => $quotation_detail->quantity,
                'price' => $quotation_detail->price,
                'weight' => 1,
                'options' => [
                    'product_discount' => $quotation_detail->product_discount_amount,
                    'product_discount_type' => $quotation_detail->product_discount_type,
                    'sub_total' => $quotation_detail->sub_total,
                    'code' => $quotation_detail->product_code,
                    'stock' => Product::findOrFail($quotation_detail->product_id)->product_quantity,
                    'product_tax' => $quotation_detail->product_tax_amount,
                    'unit_price' => $quotation_detail->unit_price,
                ],
            ]);
        }
    }

    public function delete(Quotation $quotation): void
    {
        $quotation->delete();
    }

    /**
     * The email address the quotation should be sent to.
     */
    public function recipientEmail(Quotation $quotation): ?string
    {
        return $quotation->customer->customer_email;
    }

    /**
     * Mark a quotation as sent to the customer.
     */
    public function markSent(Quotation $quotation): void
    {
        $quotation->update(['status' => 'Sent']);
    }

    /**
     * Seed the "sale" cart from a quotation's line items, so a sale can be
     * raised directly from an accepted quotation.
     */
    public function loadSaleCart(Quotation $quotation): void
    {
        Cart::instance('sale')->destroy();
        $cart = Cart::instance('sale');

        foreach ($quotation->quotationDetails as $quotation_detail) {
            $cart->add([
                'id' => $quotation_detail->product_id,
                'name' => $quotation_detail->product_name,
                'qty' => $quotation_detail->quantity,
                'price' => $quotation_detail->price,
                'weight' => 1,
                'options' => [
                    'product_discount' => $quotation_detail->product_discount_amount,
                    'product_discount_type' => $quotation_detail->product_discount_type,
                    'sub_total' => $quotation_detail->sub_total,
                    'code' => $quotation_detail->product_code,
                    'stock' => Product::findOrFail($quotation_detail->product_id)->product_quantity,
                    'product_tax' => $quotation_detail->product_tax_amount,
                    'unit_price' => $quotation_detail->unit_price,
                ],
            ]);
        }
    }

    /**
     * Persist each cart item as a quotation detail line.
     */
    private function syncDetails(Quotation $quotation, $cart): void
    {
        foreach ($cart->content() as $cart_item) {
            QuotationDetails::create([
                'quotation_id' => $quotation->id,
                'product_id' => $cart_item->id,
                'product_name' => $cart_item->name,
                'product_code' => $cart_item->options->code,
                'quantity' => $cart_item->qty,
                'price' => $cart_item->price * 100,
                'unit_price' => $cart_item->options->unit_price * 100,
                'sub_total' => $cart_item->options->sub_total * 100,
                'product_discount_amount' => $cart_item->options->product_discount * 100,
                'product_discount_type' => $cart_item->options->product_discount_type,
                'product_tax_amount' => $cart_item->options->product_tax * 100,
            ]);
        }
    }
}
