<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\PurchaseReturnPayment;
use App\Models\Supplier;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Owns the business operations for purchase returns: creating and updating a
 * return document from the cart and recording payments.
 *
 * Goods returned to the supplier leave stock through {@see StockService}
 * (reference_type=PurchaseReturn); payment status is delegated to
 * {@see PaymentStatusService}.
 */
class PurchaseReturnService
{
    public function __construct(
        private readonly StockService $stock,
        private readonly PaymentStatusService $paymentStatus,
    ) {}

    /**
     * Paginate purchase returns, optionally filtered by reference or supplier name.
     */
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return PurchaseReturn::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('reference', 'like', $term)
                    ->orWhere('supplier_name', 'like', $term);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Paginate the payments recorded against a single purchase return.
     */
    public function paginatePayments(int $purchaseReturnId, ?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return PurchaseReturnPayment::query()
            ->where('purchase_return_id', $purchaseReturnId)
            ->when($search, fn ($q) => $q->where('reference', 'like', '%'.$search.'%'))
            ->latest()
            ->paginate($perPage);
    }

    public function deletePayment(int $id): void
    {
        PurchaseReturnPayment::findOrFail($id)->delete();
    }

    public function findOrFail(int|string $id): PurchaseReturn
    {
        return PurchaseReturn::findOrFail($id);
    }

    /**
     * The supplier attached to a purchase return, for the detail view.
     */
    public function supplierFor(PurchaseReturn $purchase_return): Supplier
    {
        return Supplier::findOrFail($purchase_return->supplier_id);
    }

    /**
     * Reset the "purchase_return" cart to the given return's saved line items,
     * ready for editing.
     */
    public function loadCart(PurchaseReturn $purchase_return): void
    {
        Cart::instance('purchase_return')->destroy();
        $cart = Cart::instance('purchase_return');

        foreach ($purchase_return->purchaseReturnDetails as $purchase_return_detail) {
            $cart->add([
                'id' => $purchase_return_detail->product_id,
                'name' => $purchase_return_detail->product_name,
                'qty' => $purchase_return_detail->quantity,
                'price' => $purchase_return_detail->price,
                'weight' => 1,
                'options' => [
                    'product_discount' => $purchase_return_detail->product_discount_amount,
                    'product_discount_type' => $purchase_return_detail->product_discount_type,
                    'sub_total' => $purchase_return_detail->sub_total,
                    'code' => $purchase_return_detail->product_code,
                    'stock' => Product::findOrFail($purchase_return_detail->product_id)->product_quantity,
                    'product_tax' => $purchase_return_detail->product_tax_amount,
                    'unit_price' => $purchase_return_detail->unit_price,
                ],
            ]);
        }
    }

    public function delete(PurchaseReturn $purchase_return): void
    {
        $purchase_return->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createPurchaseReturn(array $data): PurchaseReturn
    {
        return DB::transaction(function () use ($data) {
            $cart = Cart::instance('purchase_return');

            $dueAmount = $data['total_amount'] - $data['paid_amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $data['total_amount']);

            $purchase_return = PurchaseReturn::create([
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
                $this->createReturnDetail($purchase_return, $cart_item);

                if ($data['status'] == 'Shipped' || $data['status'] == 'Completed') {
                    $this->stock->stockOut(
                        Product::findOrFail($cart_item->id),
                        (int) $cart_item->qty,
                        null,
                        'PurchaseReturn',
                        $purchase_return->id,
                    );
                }
            }

            $cart->destroy();

            if ($purchase_return->paid_amount > 0) {
                PurchaseReturnPayment::create([
                    'date' => $data['date'],
                    'reference' => 'INV/'.$purchase_return->reference,
                    'amount' => $purchase_return->paid_amount,
                    'purchase_return_id' => $purchase_return->id,
                    'payment_method' => $data['payment_method'],
                ]);
            }

            return $purchase_return;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updatePurchaseReturn(PurchaseReturn $purchase_return, array $data): PurchaseReturn
    {
        return DB::transaction(function () use ($purchase_return, $data) {
            $cart = Cart::instance('purchase_return');

            $dueAmount = $data['total_amount'] - $data['paid_amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $data['total_amount']);

            foreach ($purchase_return->purchaseReturnDetails as $purchase_return_detail) {
                if ($purchase_return->status == 'Shipped' || $purchase_return->status == 'Completed') {
                    $this->stock->stockIn(
                        Product::findOrFail($purchase_return_detail->product_id),
                        (int) $purchase_return_detail->quantity,
                        null,
                        'PurchaseReturn',
                        $purchase_return->id,
                    );
                }
                $purchase_return_detail->delete();
            }

            $purchase_return->update([
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
                $this->createReturnDetail($purchase_return, $cart_item);

                if ($data['status'] == 'Shipped' || $data['status'] == 'Completed') {
                    $this->stock->stockOut(
                        Product::findOrFail($cart_item->id),
                        (int) $cart_item->qty,
                        null,
                        'PurchaseReturn',
                        $purchase_return->id,
                    );
                }
            }

            $cart->destroy();

            return $purchase_return;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function addPayment(array $data): PurchaseReturnPayment
    {
        return DB::transaction(function () use ($data) {
            $payment = PurchaseReturnPayment::create([
                'date' => $data['date'],
                'reference' => $data['reference'],
                'amount' => $data['amount'],
                'note' => $data['note'] ?? null,
                'purchase_return_id' => $data['purchase_return_id'],
                'payment_method' => $data['payment_method'],
            ]);

            $purchase_return = PurchaseReturn::findOrFail($data['purchase_return_id']);

            $dueAmount = $purchase_return->due_amount - $data['amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $purchase_return->total_amount);

            $purchase_return->update([
                'paid_amount' => ($purchase_return->paid_amount + $data['amount']) * 100,
                'due_amount' => $dueAmount * 100,
                'payment_status' => $paymentStatus,
            ]);

            return $payment;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updatePayment(PurchaseReturnPayment $payment, array $data): PurchaseReturnPayment
    {
        return DB::transaction(function () use ($payment, $data) {
            $purchase_return = $payment->purchaseReturn;

            $dueAmount = ($purchase_return->due_amount + $payment->amount) - $data['amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $purchase_return->total_amount);

            $purchase_return->update([
                'paid_amount' => (($purchase_return->paid_amount - $payment->amount) + $data['amount']) * 100,
                'due_amount' => $dueAmount * 100,
                'payment_status' => $paymentStatus,
            ]);

            $payment->update([
                'date' => $data['date'],
                'reference' => $data['reference'],
                'amount' => $data['amount'],
                'note' => $data['note'] ?? null,
                'purchase_return_id' => $data['purchase_return_id'],
                'payment_method' => $data['payment_method'],
            ]);

            return $payment;
        });
    }

    private function createReturnDetail(PurchaseReturn $purchase_return, $cart_item): void
    {
        PurchaseReturnDetail::create([
            'purchase_return_id' => $purchase_return->id,
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
