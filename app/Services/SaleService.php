<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\SalePayment;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;

/**
 * Owns the business operations for sales: creating and updating a sale
 * document from the cart, ringing up a POS sale, and recording payments.
 *
 * Stock movements are delegated to {@see StockService} so every deduction is
 * locked, guarded against overselling and attributed to the sale in the stock
 * ledger. Payment status is delegated to {@see PaymentStatusService}.
 */
class SaleService
{
    public function __construct(
        private readonly StockService $stock,
        private readonly PaymentStatusService $paymentStatus,
    ) {}

    /**
     * Create a sale from the "sale" cart instance.
     *
     * @param  array<string, mixed>  $data
     */
    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $cart = Cart::instance('sale');

            $dueAmount = $data['total_amount'] - $data['paid_amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $data['total_amount']);

            $sale = Sale::create([
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
                $this->createSaleDetail($sale, $cart_item);

                if ($data['status'] == 'Shipped' || $data['status'] == 'Completed') {
                    $this->stock->stockOut(
                        Product::findOrFail($cart_item->id),
                        (int) $cart_item->qty,
                        null,
                        'Sale',
                        $sale->id,
                    );
                }
            }

            $cart->destroy();

            if ($sale->paid_amount > 0) {
                SalePayment::create([
                    'date' => $data['date'],
                    'reference' => 'INV/'.$sale->reference,
                    'amount' => $sale->paid_amount,
                    'sale_id' => $sale->id,
                    'payment_method' => $data['payment_method'],
                ]);
            }

            return $sale;
        });
    }

    /**
     * Ring up a point-of-sale sale. The status is always "Completed" and stock
     * is always deducted, mirroring the historic POS behaviour.
     *
     * @param  array<string, mixed>  $data
     */
    public function createPosSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $cart = Cart::instance('sale');

            $dueAmount = $data['total_amount'] - $data['paid_amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $data['total_amount']);

            $sale = Sale::create([
                'date' => now()->format('Y-m-d'),
                'reference' => 'PSL',
                'customer_id' => $data['customer_id'],
                'customer_name' => Customer::findOrFail($data['customer_id'])->customer_name,
                'tax_percentage' => $data['tax_percentage'],
                'discount_percentage' => $data['discount_percentage'],
                'shipping_amount' => $data['shipping_amount'] * 100,
                'paid_amount' => $data['paid_amount'] * 100,
                'total_amount' => $data['total_amount'] * 100,
                'due_amount' => $dueAmount * 100,
                'status' => 'Completed',
                'payment_status' => $paymentStatus,
                'payment_method' => $data['payment_method'] ?? null,
                'note' => $data['note'] ?? null,
                'tax_amount' => (float) $cart->tax() * 100,
                'discount_amount' => (float) $cart->discount() * 100,
            ]);

            foreach ($cart->content() as $cart_item) {
                $this->createSaleDetail($sale, $cart_item);

                $this->stock->stockOut(
                    Product::findOrFail($cart_item->id),
                    (int) $cart_item->qty,
                    null,
                    'Sale',
                    $sale->id,
                );
            }

            $cart->destroy();

            if ($sale->paid_amount > 0) {
                SalePayment::create([
                    'date' => now()->format('Y-m-d'),
                    'reference' => 'INV/'.$sale->reference,
                    'amount' => $sale->paid_amount,
                    'sale_id' => $sale->id,
                    'payment_method' => $data['payment_method'] ?? null,
                ]);
            }

            return $sale;
        });
    }

    /**
     * Update a sale: reverse the stock effect of the previous lines, then
     * rebuild the sale and re-apply stock from the "sale" cart instance.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateSale(Sale $sale, array $data): Sale
    {
        return DB::transaction(function () use ($sale, $data) {
            $cart = Cart::instance('sale');

            $dueAmount = $data['total_amount'] - $data['paid_amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $data['total_amount']);

            foreach ($sale->saleDetails as $sale_detail) {
                if ($sale->status == 'Shipped' || $sale->status == 'Completed') {
                    $this->stock->stockIn(
                        Product::findOrFail($sale_detail->product_id),
                        (int) $sale_detail->quantity,
                        null,
                        'Sale',
                        $sale->id,
                    );
                }
                $sale_detail->delete();
            }

            $sale->update([
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
                $this->createSaleDetail($sale, $cart_item);

                if ($data['status'] == 'Shipped' || $data['status'] == 'Completed') {
                    $this->stock->stockOut(
                        Product::findOrFail($cart_item->id),
                        (int) $cart_item->qty,
                        null,
                        'Sale',
                        $sale->id,
                    );
                }
            }

            $cart->destroy();

            return $sale;
        });
    }

    /**
     * Record a payment against a sale and recompute its payment status.
     *
     * @param  array<string, mixed>  $data
     */
    public function addPayment(array $data): SalePayment
    {
        return DB::transaction(function () use ($data) {
            $payment = SalePayment::create([
                'date' => $data['date'],
                'reference' => $data['reference'],
                'amount' => $data['amount'],
                'note' => $data['note'] ?? null,
                'sale_id' => $data['sale_id'],
                'payment_method' => $data['payment_method'],
            ]);

            $sale = Sale::findOrFail($data['sale_id']);

            $dueAmount = $sale->due_amount - $data['amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $sale->total_amount);

            $sale->update([
                'paid_amount' => ($sale->paid_amount + $data['amount']) * 100,
                'due_amount' => $dueAmount * 100,
                'payment_status' => $paymentStatus,
            ]);

            return $payment;
        });
    }

    /**
     * Update an existing sale payment and recompute the sale's payment status.
     *
     * @param  array<string, mixed>  $data
     */
    public function updatePayment(SalePayment $payment, array $data): SalePayment
    {
        return DB::transaction(function () use ($payment, $data) {
            $sale = $payment->sale;

            $dueAmount = ($sale->due_amount + $payment->amount) - $data['amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $sale->total_amount);

            $sale->update([
                'paid_amount' => (($sale->paid_amount - $payment->amount) + $data['amount']) * 100,
                'due_amount' => $dueAmount * 100,
                'payment_status' => $paymentStatus,
            ]);

            $payment->update([
                'date' => $data['date'],
                'reference' => $data['reference'],
                'amount' => $data['amount'],
                'note' => $data['note'] ?? null,
                'sale_id' => $data['sale_id'],
                'payment_method' => $data['payment_method'],
            ]);

            return $payment;
        });
    }

    /**
     * Persist a single sale line from a cart item.
     */
    private function createSaleDetail(Sale $sale, $cart_item): void
    {
        SaleDetails::create([
            'sale_id' => $sale->id,
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
