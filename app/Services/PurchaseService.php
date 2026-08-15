<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;

/**
 * Owns the business operations for purchases: creating and updating a purchase
 * document from the cart and recording supplier payments.
 *
 * Stock increases are delegated to {@see StockService} so every movement is
 * locked and attributed to the purchase; payment status is delegated to
 * {@see PaymentStatusService}.
 */
class PurchaseService
{
    public function __construct(
        private readonly StockService $stock,
        private readonly PaymentStatusService $paymentStatus,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function createPurchase(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $cart = Cart::instance('purchase');

            $dueAmount = $data['total_amount'] - $data['paid_amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $data['total_amount']);

            $purchase = Purchase::create([
                'date' => $data['date'],
                'supplier_id' => $data['supplier_id'],
                'supplier_name' => Supplier::findOrFail($data['supplier_id'])->supplier_name,
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
                $this->createPurchaseDetail($purchase, $cart_item);

                if ($data['status'] == 'Completed') {
                    $this->stock->stockIn(
                        Product::findOrFail($cart_item->id),
                        (int) $cart_item->qty,
                        null,
                        'Purchase',
                        $purchase->id,
                    );
                }
            }

            $cart->destroy();

            if ($purchase->paid_amount > 0) {
                PurchasePayment::create([
                    'date' => $data['date'],
                    'reference' => 'INV/'.$purchase->reference,
                    'amount' => $purchase->paid_amount,
                    'purchase_id' => $purchase->id,
                    'payment_method' => $data['payment_method'],
                ]);
            }

            return $purchase;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updatePurchase(Purchase $purchase, array $data): Purchase
    {
        return DB::transaction(function () use ($purchase, $data) {
            $cart = Cart::instance('purchase');

            $dueAmount = $data['total_amount'] - $data['paid_amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $data['total_amount']);

            foreach ($purchase->purchaseDetails as $purchase_detail) {
                if ($purchase->status == 'Completed') {
                    $this->stock->stockOut(
                        Product::findOrFail($purchase_detail->product_id),
                        (int) $purchase_detail->quantity,
                        null,
                        'Purchase',
                        $purchase->id,
                    );
                }
                $purchase_detail->delete();
            }

            $purchase->update([
                'date' => $data['date'],
                'reference' => $data['reference'],
                'supplier_id' => $data['supplier_id'],
                'supplier_name' => Supplier::findOrFail($data['supplier_id'])->supplier_name,
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
                $this->createPurchaseDetail($purchase, $cart_item);

                if ($data['status'] == 'Completed') {
                    $this->stock->stockIn(
                        Product::findOrFail($cart_item->id),
                        (int) $cart_item->qty,
                        null,
                        'Purchase',
                        $purchase->id,
                    );
                }
            }

            $cart->destroy();

            return $purchase;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function addPayment(array $data): PurchasePayment
    {
        return DB::transaction(function () use ($data) {
            $payment = PurchasePayment::create([
                'date' => $data['date'],
                'reference' => $data['reference'],
                'amount' => $data['amount'],
                'note' => $data['note'] ?? null,
                'purchase_id' => $data['purchase_id'],
                'payment_method' => $data['payment_method'],
            ]);

            $purchase = Purchase::findOrFail($data['purchase_id']);

            $dueAmount = $purchase->due_amount - $data['amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $purchase->total_amount);

            $purchase->update([
                'paid_amount' => ($purchase->paid_amount + $data['amount']) * 100,
                'due_amount' => $dueAmount * 100,
                'payment_status' => $paymentStatus,
            ]);

            return $payment;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updatePayment(PurchasePayment $payment, array $data): PurchasePayment
    {
        return DB::transaction(function () use ($payment, $data) {
            $purchase = $payment->purchase;

            $dueAmount = ($purchase->due_amount + $payment->amount) - $data['amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $purchase->total_amount);

            $purchase->update([
                'paid_amount' => (($purchase->paid_amount - $payment->amount) + $data['amount']) * 100,
                'due_amount' => $dueAmount * 100,
                'payment_status' => $paymentStatus,
            ]);

            $payment->update([
                'date' => $data['date'],
                'reference' => $data['reference'],
                'amount' => $data['amount'],
                'note' => $data['note'] ?? null,
                'purchase_id' => $data['purchase_id'],
                'payment_method' => $data['payment_method'],
            ]);

            return $payment;
        });
    }

    private function createPurchaseDetail(Purchase $purchase, $cart_item): void
    {
        PurchaseDetail::create([
            'purchase_id' => $purchase->id,
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
