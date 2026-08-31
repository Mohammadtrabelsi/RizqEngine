<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetail;
use App\Models\SaleReturnPayment;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Owns the business operations for sale returns: creating and updating a
 * return document from the cart and recording customer refunds/payments.
 *
 * Returned goods flow back into stock through {@see StockService}
 * (reference_type=SaleReturn); payment status is delegated to
 * {@see PaymentStatusService}.
 */
class SaleReturnService
{
    public function __construct(
        private readonly StockService $stock,
        private readonly PaymentStatusService $paymentStatus,
    ) {}

    /**
     * Paginate sale returns, optionally filtered by reference or customer name.
     */
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return SaleReturn::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('reference', 'like', $term)
                    ->orWhere('customer_name', 'like', $term);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Paginate the payments recorded against a single sale return.
     */
    public function paginatePayments(int $saleReturnId, ?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return SaleReturnPayment::query()
            ->where('sale_return_id', $saleReturnId)
            ->when($search, fn ($q) => $q->where('reference', 'like', '%'.$search.'%'))
            ->latest()
            ->paginate($perPage);
    }

    public function deletePayment(int $id): void
    {
        SaleReturnPayment::findOrFail($id)->delete();
    }

    public function findOrFail(int|string $id): SaleReturn
    {
        return SaleReturn::findOrFail($id);
    }

    /**
     * The customer attached to a sale return, for the detail view.
     */
    public function customerFor(SaleReturn $sale_return): Customer
    {
        return Customer::findOrFail($sale_return->customer_id);
    }

    /**
     * Reset the "sale_return" cart to the given return's saved line items,
     * ready for editing.
     */
    public function loadCart(SaleReturn $sale_return): void
    {
        Cart::instance('sale_return')->destroy();
        $cart = Cart::instance('sale_return');

        foreach ($sale_return->saleReturnDetails as $sale_return_detail) {
            $cart->add([
                'id' => $sale_return_detail->product_id,
                'name' => $sale_return_detail->product_name,
                'qty' => $sale_return_detail->quantity,
                'price' => $sale_return_detail->price,
                'weight' => 1,
                'options' => [
                    'product_discount' => $sale_return_detail->product_discount_amount,
                    'product_discount_type' => $sale_return_detail->product_discount_type,
                    'sub_total' => $sale_return_detail->sub_total,
                    'code' => $sale_return_detail->product_code,
                    'stock' => Product::findOrFail($sale_return_detail->product_id)->product_quantity,
                    'product_tax' => $sale_return_detail->product_tax_amount,
                    'unit_price' => $sale_return_detail->unit_price,
                ],
            ]);
        }
    }

    public function delete(SaleReturn $sale_return): void
    {
        $sale_return->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createSaleReturn(array $data): SaleReturn
    {
        return DB::transaction(function () use ($data) {
            $cart = Cart::instance('sale_return');

            $dueAmount = $data['total_amount'] - $data['paid_amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $data['total_amount']);

            $sale_return = SaleReturn::create([
                'date' => $data['date'],
                'customer_id' => $data['customer_id'],
                'customer_name' => Customer::findOrFail($data['customer_id'])->customer_name,
                'tax_percentage' => $data['tax_percentage'],
                'discount_percentage' => $data['discount_percentage'],
                'shipping_amount' => $data['shipping_amount'] * 100,
                'paid_amount' => $data['paid_amount'] * 100,
                'total_amount' => $data['total_amount'] * 100,
                'due_amount' => $dueAmount * 100,
                'status' => $data['status'],
                'payment_status' => $paymentStatus,
                'payment_method' => $data['payment_method'],
                'note' => $data['note'] ?? null,
                'tax_amount' => (float) $cart->tax() * 100,
                'discount_amount' => (float) $cart->discount() * 100,
            ]);

            foreach ($cart->content() as $cart_item) {
                $this->createReturnDetail($sale_return, $cart_item);

                if ($data['status'] == 'Completed') {
                    $this->stock->stockIn(
                        Product::findOrFail($cart_item->id),
                        (int) $cart_item->qty,
                        null,
                        'SaleReturn',
                        $sale_return->id,
                    );
                }
            }

            $cart->destroy();

            if ($sale_return->paid_amount > 0) {
                SaleReturnPayment::create([
                    'date' => $data['date'],
                    'reference' => 'INV/'.$sale_return->reference,
                    'amount' => $sale_return->paid_amount,
                    'sale_return_id' => $sale_return->id,
                    'payment_method' => $data['payment_method'],
                ]);
            }

            return $sale_return;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateSaleReturn(SaleReturn $sale_return, array $data): SaleReturn
    {
        return DB::transaction(function () use ($sale_return, $data) {
            $cart = Cart::instance('sale_return');

            $dueAmount = $data['total_amount'] - $data['paid_amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $data['total_amount']);

            foreach ($sale_return->saleReturnDetails as $sale_return_detail) {
                if ($sale_return->status == 'Completed') {
                    $this->stock->stockOut(
                        Product::findOrFail($sale_return_detail->product_id),
                        (int) $sale_return_detail->quantity,
                        null,
                        'SaleReturn',
                        $sale_return->id,
                    );
                }
                $sale_return_detail->delete();
            }

            $sale_return->update([
                'date' => $data['date'],
                'reference' => $data['reference'],
                'customer_id' => $data['customer_id'],
                'customer_name' => Customer::findOrFail($data['customer_id'])->customer_name,
                'tax_percentage' => $data['tax_percentage'],
                'discount_percentage' => $data['discount_percentage'],
                'shipping_amount' => $data['shipping_amount'] * 100,
                'paid_amount' => $data['paid_amount'] * 100,
                'total_amount' => $data['total_amount'] * 100,
                'due_amount' => $dueAmount * 100,
                'status' => $data['status'],
                'payment_status' => $paymentStatus,
                'payment_method' => $data['payment_method'],
                'note' => $data['note'] ?? null,
                'tax_amount' => (float) $cart->tax() * 100,
                'discount_amount' => (float) $cart->discount() * 100,
            ]);

            foreach ($cart->content() as $cart_item) {
                $this->createReturnDetail($sale_return, $cart_item);

                if ($data['status'] == 'Completed') {
                    $this->stock->stockIn(
                        Product::findOrFail($cart_item->id),
                        (int) $cart_item->qty,
                        null,
                        'SaleReturn',
                        $sale_return->id,
                    );
                }
            }

            $cart->destroy();

            return $sale_return;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function addPayment(array $data): SaleReturnPayment
    {
        return DB::transaction(function () use ($data) {
            $payment = SaleReturnPayment::create([
                'date' => $data['date'],
                'reference' => $data['reference'],
                'amount' => $data['amount'],
                'note' => $data['note'] ?? null,
                'sale_return_id' => $data['sale_return_id'],
                'payment_method' => $data['payment_method'],
            ]);

            $sale_return = SaleReturn::findOrFail($data['sale_return_id']);

            $dueAmount = $sale_return->due_amount - $data['amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $sale_return->total_amount);

            $sale_return->update([
                'paid_amount' => ($sale_return->paid_amount + $data['amount']) * 100,
                'due_amount' => $dueAmount * 100,
                'payment_status' => $paymentStatus,
            ]);

            return $payment;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updatePayment(SaleReturnPayment $payment, array $data): SaleReturnPayment
    {
        return DB::transaction(function () use ($payment, $data) {
            $sale_return = $payment->saleReturn;

            $dueAmount = ($sale_return->due_amount + $payment->amount) - $data['amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $sale_return->total_amount);

            $sale_return->update([
                'paid_amount' => (($sale_return->paid_amount - $payment->amount) + $data['amount']) * 100,
                'due_amount' => $dueAmount * 100,
                'payment_status' => $paymentStatus,
            ]);

            $payment->update([
                'date' => $data['date'],
                'reference' => $data['reference'],
                'amount' => $data['amount'],
                'note' => $data['note'] ?? null,
                'sale_return_id' => $data['sale_return_id'],
                'payment_method' => $data['payment_method'],
            ]);

            return $payment;
        });
    }

    private function createReturnDetail(SaleReturn $sale_return, $cart_item): void
    {
        SaleReturnDetail::create([
            'sale_return_id' => $sale_return->id,
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
