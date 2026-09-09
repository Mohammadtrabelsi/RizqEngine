<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\PurchasePayment;
use App\Models\PurchaseWithholdingTax;
use App\Models\Supplier;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
        private readonly WithholdingTaxCalculator $withholdingCalculator,
    ) {}

    /**
     * Multiplier used to persist withholding amounts as integers in millimes
     * (× 1000), preserving the Tunisian dinar's three-decimal precision.
     */
    private const WITHHOLDING_SCALE = 1000;

    /**
     * Compute the withholding (retenue à la source) breakdown for the purchase
     * cart from the selected withholding-tax ids and the document totals.
     *
     * @param  array<string, mixed>  $data
     * @return array{lines: array<int, array<string, mixed>>, total: float, net_payable: float}
     */
    private function resolveWithholding(array $data, float $ttc, float $tva): array
    {
        $ht = $ttc - $tva;
        $ids = (array) ($data['withholding_tax_ids'] ?? []);

        $result = $this->withholdingCalculator->calculateForIds($ids, $ht, $tva, $ttc);

        return [
            'lines' => $result['lines'],
            'total' => $result['total'],
            'net_payable' => $result['net_payable'],
        ];
    }

    /**
     * Persist the withholding snapshot lines for a purchase.
     *
     * @param  array<int, array<string, mixed>>  $lines
     */
    private function saveWithholdingLines(Purchase $purchase, array $lines): void
    {
        foreach ($lines as $line) {
            PurchaseWithholdingTax::create([
                'purchase_id' => $purchase->id,
                'withholding_tax_id' => $line['withholding_tax_id'],
                'name' => $line['name'],
                'code' => $line['code'],
                'rate' => $line['rate'],
                'calculation_base' => $line['calculation_base'],
                'taxable_amount' => (int) round($line['taxable_amount'] * self::WITHHOLDING_SCALE),
                'amount' => (int) round($line['amount'] * self::WITHHOLDING_SCALE),
            ]);
        }
    }

    /**
     * Paginate purchases, optionally filtered by reference or supplier name.
     */
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return Purchase::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('reference', 'like', $term)
                    ->orWhere('supplier_name', 'like', $term);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Paginate the payments recorded against a single purchase.
     */
    public function paginatePayments(int $purchaseId, ?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return PurchasePayment::query()
            ->where('purchase_id', $purchaseId)
            ->when($search, fn ($q) => $q->where('reference', 'like', '%'.$search.'%'))
            ->latest()
            ->paginate($perPage);
    }

    public function deletePayment(int $id): void
    {
        PurchasePayment::findOrFail($id)->delete();
    }

    public function findOrFail(int|string $id): Purchase
    {
        return Purchase::findOrFail($id);
    }

    /**
     * The supplier attached to a purchase, for the detail view.
     */
    public function supplierFor(Purchase $purchase): Supplier
    {
        return Supplier::findOrFail($purchase->supplier_id);
    }

    /**
     * Reset the "purchase" cart to the given purchase's saved line items,
     * ready for editing.
     */
    public function loadCart(Purchase $purchase): void
    {
        Cart::instance('purchase')->destroy();
        $cart = Cart::instance('purchase');

        foreach ($purchase->purchaseDetails as $purchase_detail) {
            $cart->add([
                'id' => $purchase_detail->product_id,
                'name' => $purchase_detail->product_name,
                'qty' => $purchase_detail->quantity,
                'price' => $purchase_detail->price,
                'weight' => 1,
                'options' => [
                    'product_discount' => $purchase_detail->product_discount_amount,
                    'product_discount_type' => $purchase_detail->product_discount_type,
                    'sub_total' => $purchase_detail->sub_total,
                    'code' => $purchase_detail->product_code,
                    'stock' => Product::findOrFail($purchase_detail->product_id)->product_quantity,
                    'product_tax' => $purchase_detail->product_tax_amount,
                    'unit_price' => $purchase_detail->unit_price,
                ],
            ]);
        }
    }

    public function delete(Purchase $purchase): void
    {
        $purchase->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createPurchase(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $cart = Cart::instance('purchase');

            // Retenue à la source is deducted after the TTC: the supplier is
            // owed the net (TTC − RAS), so the due balance tracks the net, not
            // the gross. With no withholding selected the net equals the TTC
            // and behaviour is unchanged for existing purchases.
            $withholding = $this->resolveWithholding(
                $data,
                (float) $data['total_amount'],
                (float) $cart->tax(),
            );

            $dueAmount = $withholding['net_payable'] - $data['paid_amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $withholding['net_payable']);

            $purchase = Purchase::create([
                'date' => $data['date'],
                'supplier_id' => $data['supplier_id'],
                'supplier_name' => Supplier::findOrFail($data['supplier_id'])->supplier_name,
                'warehouse_id' => $data['warehouse_id'] ?? null,
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
                'withholding_amount' => (int) round($withholding['total'] * self::WITHHOLDING_SCALE),
                'discount_amount' => (float) $cart->discount() * 100,
            ]);

            $this->saveWithholdingLines($purchase, $withholding['lines']);

            foreach ($cart->content() as $cart_item) {
                $this->createPurchaseDetail($purchase, $cart_item);

                if ($data['status'] == 'Completed') {
                    $this->stock->stockIn(
                        Product::findOrFail($cart_item->id),
                        (int) $cart_item->qty,
                        null,
                        'Purchase',
                        $purchase->id,
                        $purchase->warehouse_id,
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

            $withholding = $this->resolveWithholding(
                $data,
                (float) $data['total_amount'],
                (float) $cart->tax(),
            );

            $dueAmount = $withholding['net_payable'] - $data['paid_amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $withholding['net_payable']);

            // Replace the previous withholding snapshot with a freshly computed
            // one so an edited document stays consistent with its taxes.
            $purchase->withholdingTaxes()->delete();

            foreach ($purchase->purchaseDetails as $purchase_detail) {
                if ($purchase->status == 'Completed') {
                    $this->stock->stockOut(
                        Product::findOrFail($purchase_detail->product_id),
                        (int) $purchase_detail->quantity,
                        null,
                        'Purchase',
                        $purchase->id,
                        $purchase->warehouse_id,
                    );
                }
                $purchase_detail->delete();
            }

            $purchase->update([
                'date' => $data['date'],
                'reference' => $data['reference'],
                'supplier_id' => $data['supplier_id'],
                'supplier_name' => Supplier::findOrFail($data['supplier_id'])->supplier_name,
                'warehouse_id' => $data['warehouse_id'] ?? null,
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
                'withholding_amount' => (int) round($withholding['total'] * self::WITHHOLDING_SCALE),
                'discount_amount' => (float) $cart->discount() * 100,
            ]);

            $this->saveWithholdingLines($purchase, $withholding['lines']);

            foreach ($cart->content() as $cart_item) {
                $this->createPurchaseDetail($purchase, $cart_item);

                if ($data['status'] == 'Completed') {
                    $this->stock->stockIn(
                        Product::findOrFail($cart_item->id),
                        (int) $cart_item->qty,
                        null,
                        'Purchase',
                        $purchase->id,
                        $purchase->warehouse_id,
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
