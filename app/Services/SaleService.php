<?php

namespace App\Services;

use App\Exceptions\ConversionException;
use App\Exceptions\InsufficientStockException;
use App\Models\BonLivraison;
use App\Models\Commande;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\SalePayment;
use App\Models\SaleWithholdingTax;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
        private readonly CustomerCreditService $credit,
        private readonly WithholdingTaxCalculator $withholdingCalculator,
    ) {}

    /**
     * Multiplier used to persist withholding amounts as integers in millimes
     * (× 1000), preserving the Tunisian dinar's three-decimal precision.
     */
    private const WITHHOLDING_SCALE = 1000;

    /**
     * Compute the withholding (retenue à la source) breakdown for the sale cart
     * from the selected withholding-tax ids and the document totals.
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
     * Persist the withholding snapshot lines for a sale.
     *
     * @param  array<int, array<string, mixed>>  $lines
     */
    private function saveWithholdingLines(Sale $sale, array $lines): void
    {
        foreach ($lines as $line) {
            SaleWithholdingTax::create([
                'sale_id' => $sale->id,
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
     * Paginate sales, optionally filtered by reference/customer name, status,
     * payment status, and/or a date range.
     *
     * @param  array{status?: string, payment_status?: string, date_from?: string, date_to?: string}  $filters
     */
    public function paginate(?string $search = null, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return Sale::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('reference', 'like', $term)
                    ->orWhere('customer_name', 'like', $term);
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['payment_status'] ?? null, fn ($query, $status) => $query->where('payment_status', $status))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('date', '<=', $date))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Paginate the payments recorded against a single sale.
     */
    public function paginatePayments(int $saleId, ?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return SalePayment::query()
            ->where('sale_id', $saleId)
            ->when($search, fn ($q) => $q->where('reference', 'like', '%'.$search.'%'))
            ->latest()
            ->paginate($perPage);
    }

    public function deletePayment(int $id): void
    {
        SalePayment::findOrFail($id)->delete();
    }

    public function findOrFail(int|string $id): Sale
    {
        return Sale::findOrFail($id);
    }

    /**
     * The customer attached to a sale, for the detail view.
     */
    public function customerFor(Sale $sale): Customer
    {
        return Customer::findOrFail($sale->customer_id);
    }

    /**
     * Reset the "sale" cart to the given sale's saved line items, ready for
     * editing.
     */
    public function loadCart(Sale $sale): void
    {
        Cart::instance('sale')->destroy();
        $cart = Cart::instance('sale');

        foreach ($sale->saleDetails as $sale_detail) {
            $cart->add([
                'id' => $sale_detail->product_id,
                'name' => $sale_detail->product_name,
                'qty' => $sale_detail->quantity,
                'price' => $sale_detail->price,
                'weight' => 1,
                'options' => [
                    'product_discount' => $sale_detail->product_discount_amount,
                    'product_discount_type' => $sale_detail->product_discount_type,
                    'sub_total' => $sale_detail->sub_total,
                    'code' => $sale_detail->product_code,
                    'stock' => Product::findOrFail($sale_detail->product_id)->product_quantity,
                    'product_tax' => $sale_detail->product_tax_amount,
                    'unit_price' => $sale_detail->unit_price,
                ],
            ]);
        }
    }

    public function delete(Sale $sale): void
    {
        $sale->delete();
    }

    /**
     * Create a sale from the "sale" cart instance.
     *
     * @param  array<string, mixed>  $data
     */
    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $cart = Cart::instance('sale');

            // Retenue à la source is deducted after the TTC: the customer keeps
            // the RAS and settles only the net (TTC − RAS), so the due balance
            // tracks the net, not the gross. With no withholding selected the
            // net equals the TTC and behaviour is unchanged for existing sales.
            $withholding = $this->resolveWithholding(
                $data,
                (float) $data['total_amount'],
                (float) $cart->tax(),
            );

            $dueAmount = $withholding['net_payable'] - $data['paid_amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $withholding['net_payable']);

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
                'withholding_amount' => (int) round($withholding['total'] * self::WITHHOLDING_SCALE),
                'discount_amount' => (float) $cart->discount() * 100,
            ]);

            $this->saveWithholdingLines($sale, $withholding['lines']);

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
     * Build an invoice straight from product lines, without the cart and
     * without deducting stock.
     *
     * Used by the consignment (dépôt-vente) régularisation: the goods already
     * left the inventory when the Bon de Sortie was issued, so the sold portion
     * must be billed but NOT destocked a second time. Amounts are stored in
     * cents to match the rest of the sales pipeline.
     *
     * @param  array<int, array{product:Product, quantity:int, unit_price:int}>  $lines
     *                                                                                   unit_price is expressed in cents.
     * @param  array<string, mixed>  $meta  date, note, payment_method
     */
    public function createInvoiceFromLines(Customer $customer, array $lines, array $meta = []): Sale
    {
        return DB::transaction(function () use ($customer, $lines, $meta) {
            $totalCents = 0;
            foreach ($lines as $line) {
                $totalCents += (int) $line['unit_price'] * (int) $line['quantity'];
            }

            $sale = Sale::create([
                'date' => $meta['date'] ?? now()->toDateString(),
                'customer_id' => $customer->id,
                'customer_name' => $customer->customer_name,
                'tax_percentage' => 0,
                'discount_percentage' => 0,
                'shipping_amount' => 0,
                'paid_amount' => 0,
                'total_amount' => $totalCents,
                'due_amount' => $totalCents,
                'status' => 'Completed',
                'payment_status' => $this->paymentStatus->resolve($totalCents, $totalCents),
                'payment_method' => $meta['payment_method'] ?? 'Cash',
                'note' => $meta['note'] ?? null,
                'tax_amount' => 0,
                'discount_amount' => 0,
            ]);

            foreach ($lines as $line) {
                $product = $line['product'];
                $quantity = (int) $line['quantity'];
                $unitPrice = (int) $line['unit_price'];
                $subTotal = $unitPrice * $quantity;

                SaleDetails::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->product_name,
                    'product_code' => $product->product_code,
                    'quantity' => $quantity,
                    'price' => $unitPrice,
                    'unit_price' => $unitPrice,
                    'sub_total' => $subTotal,
                    'product_discount_amount' => 0,
                    'product_discount_type' => 'fixed',
                    'product_tax_amount' => 0,
                ]);
            }

            return $sale->refresh();
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

            $customer = Customer::findOrFail($data['customer_id']);

            // Block the register when the credit portion of this sale would push
            // the customer past their approved limit; this throws and rolls the
            // whole sale back before any stock is removed.
            if ($dueAmount > 0) {
                $this->credit->charge($customer, (int) round($dueAmount * 100));
            }

            $sale = Sale::create([
                'date' => now()->format('Y-m-d'),
                'reference' => 'PSL',
                'customer_id' => $data['customer_id'],
                'customer_name' => $customer->customer_name,
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

            $withholding = $this->resolveWithholding(
                $data,
                (float) $data['total_amount'],
                (float) $cart->tax(),
            );

            $dueAmount = $withholding['net_payable'] - $data['paid_amount'];
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $withholding['net_payable']);

            // Replace the previous withholding snapshot with a freshly computed
            // one so an edited document stays consistent with its taxes.
            $sale->withholdingTaxes()->delete();

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

            // The legal invoice number (reference) is immutable and is never
            // taken from user input on update.
            $sale->update([
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
                'withholding_amount' => (int) round($withholding['total'] * self::WITHHOLDING_SCALE),
                'discount_amount' => (float) $cart->discount() * 100,
            ]);

            $this->saveWithholdingLines($sale, $withholding['lines']);

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
     * Generate the Facture (Sale) for a confirmed Commande — the final step of
     * the Devis → Bon de Commande → Commande → Facture workflow.
     *
     * This reuses the existing Sale/invoice implementation rather than adding a
     * second one: the resulting document is an ordinary Sale, linked back to its
     * Commande via commande_id (unique => a Commande is invoiced at most once).
     * The raw integer-cent columns are copied verbatim from the Commande so the
     * amounts and taxes match exactly.
     *
     * The Facture is created as a "Pending" sale by default, so it does not move
     * stock on generation; it can then be completed through the normal Sale
     * flow, which handles stock via {@see StockService}. Pass an override to
     * change status / payment on generation.
     *
     * @param  array<string, mixed>  $overrides
     *
     * @throws ConversionException when the Commande is not invoiceable or has
     *                             already been invoiced.
     * @throws InsufficientStockException when generating with a stock-moving
     *                                    status and a product lacks stock.
     */
    public function createFactureFromCommande(Commande $commande, array $overrides = []): Sale
    {
        return DB::transaction(function () use ($commande, $overrides) {
            // Serialize concurrent invoicing of the same Commande.
            $commande = Commande::lockForUpdate()->findOrFail($commande->id);

            if (! in_array($commande->status, [Commande::STATUS_PENDING, Commande::STATUS_CONFIRMED], true)) {
                throw new ConversionException(trans('commande.not-invoiceable'));
            }

            if ($commande->sale()->exists()) {
                throw new ConversionException(
                    trans('commande.already-invoiced', ['reference' => $commande->reference])
                );
            }

            $status = $overrides['status'] ?? 'Pending';
            $paidAmount = (float) ($overrides['paid_amount'] ?? 0);
            $totalAmount = $commande->total_amount; // accessor => real units
            $dueAmount = $totalAmount - $paidAmount;
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $totalAmount);

            $sale = new Sale;
            $sale->commande_id = $commande->id;
            $sale->date = $overrides['date'] ?? now()->format('Y-m-d');
            $sale->customer_id = $commande->customer_id;
            $sale->customer_name = $commande->customer_name;
            $sale->tax_percentage = $commande->getRawOriginal('tax_percentage');
            $sale->discount_percentage = $commande->getRawOriginal('discount_percentage');
            $sale->tax_amount = $commande->getRawOriginal('tax_amount');
            $sale->discount_amount = $commande->getRawOriginal('discount_amount');
            $sale->shipping_amount = $commande->getRawOriginal('shipping_amount');
            $sale->total_amount = $commande->getRawOriginal('total_amount');
            $sale->paid_amount = $paidAmount * 100;
            $sale->due_amount = $dueAmount * 100;
            $sale->status = $status;
            $sale->payment_status = $paymentStatus;
            $sale->payment_method = $overrides['payment_method'] ?? 'Cash';
            $sale->note = $overrides['note'] ?? $commande->getRawOriginal('note');
            $sale->save();

            foreach ($commande->commandeDetails as $detail) {
                SaleDetails::create([
                    'sale_id' => $sale->id,
                    'product_id' => $detail->getRawOriginal('product_id'),
                    'product_name' => $detail->getRawOriginal('product_name'),
                    'product_code' => $detail->getRawOriginal('product_code'),
                    'quantity' => $detail->getRawOriginal('quantity'),
                    'price' => $detail->getRawOriginal('price'),
                    'unit_price' => $detail->getRawOriginal('unit_price'),
                    'sub_total' => $detail->getRawOriginal('sub_total'),
                    'product_discount_amount' => $detail->getRawOriginal('product_discount_amount'),
                    'product_discount_type' => $detail->getRawOriginal('product_discount_type'),
                    'product_tax_amount' => $detail->getRawOriginal('product_tax_amount'),
                ]);

                if ($status === 'Shipped' || $status === 'Completed') {
                    $this->stock->stockOut(
                        Product::findOrFail($detail->product_id),
                        (int) $detail->quantity,
                        null,
                        'Sale',
                        $sale->id,
                    );
                }
            }

            if ($paidAmount > 0) {
                SalePayment::create([
                    'date' => $sale->date,
                    'reference' => 'INV/'.$sale->reference,
                    'amount' => $sale->paid_amount,
                    'sale_id' => $sale->id,
                    'payment_method' => $sale->payment_method,
                ]);
            }

            $commande->update(['status' => Commande::STATUS_INVOICED]);

            return $sale->refresh();
        });
    }

    /**
     * Generate a Facture (Sale) from a Bon de Livraison — the optional last step
     * of the Devis → Commande → Bon de Livraison → Facture path.
     *
     * Like {@see self::createFactureFromCommande()} this reuses the existing
     * Sale/invoice implementation and copies the raw integer-cent columns
     * verbatim. When the delivery note originates from a Commande the resulting
     * Sale is linked to both the Bon de Livraison and that Commande, so the
     * unique commande_id / bon_livraison_id indexes together guarantee the order
     * is invoiced at most once regardless of which path is used, and the source
     * Commande is marked invoiced.
     *
     * The Facture is created as a "Pending" sale by default, so it does not move
     * stock on generation; it can then be completed through the normal Sale flow.
     *
     * @param  array<string, mixed>  $overrides
     *
     * @throws ConversionException when the Bon de Livraison has already been
     *                             invoiced.
     * @throws InsufficientStockException when generating with a stock-moving
     *                                    status and a product lacks stock.
     */
    public function createFactureFromBonLivraison(BonLivraison $bonLivraison, array $overrides = []): Sale
    {
        return DB::transaction(function () use ($bonLivraison, $overrides) {
            // Serialize concurrent invoicing of the same Bon de Livraison.
            $bonLivraison = BonLivraison::lockForUpdate()->findOrFail($bonLivraison->id);

            if ($bonLivraison->sale()->exists()) {
                throw new ConversionException(
                    trans('bonlivraison.already-invoiced', ['reference' => $bonLivraison->reference])
                );
            }

            // Guard the shared Commande against a second Facture via either path.
            if ($bonLivraison->commande_id !== null
                && Sale::where('commande_id', $bonLivraison->commande_id)->exists()) {
                throw new ConversionException(
                    trans('bonlivraison.already-invoiced', ['reference' => $bonLivraison->reference])
                );
            }

            $status = $overrides['status'] ?? 'Pending';
            $paidAmount = (float) ($overrides['paid_amount'] ?? 0);
            $totalAmount = $bonLivraison->total_amount; // accessor => real units
            $dueAmount = $totalAmount - $paidAmount;
            $paymentStatus = $this->paymentStatus->resolve($dueAmount, $totalAmount);

            $sale = new Sale;
            $sale->commande_id = $bonLivraison->commande_id;
            $sale->bon_livraison_id = $bonLivraison->id;
            $sale->date = $overrides['date'] ?? now()->format('Y-m-d');
            $sale->customer_id = $bonLivraison->customer_id;
            $sale->customer_name = $bonLivraison->customer_name;
            $sale->tax_percentage = $bonLivraison->getRawOriginal('tax_percentage');
            $sale->discount_percentage = $bonLivraison->getRawOriginal('discount_percentage');
            $sale->tax_amount = $bonLivraison->getRawOriginal('tax_amount');
            $sale->discount_amount = $bonLivraison->getRawOriginal('discount_amount');
            $sale->shipping_amount = $bonLivraison->getRawOriginal('shipping_amount');
            $sale->total_amount = $bonLivraison->getRawOriginal('total_amount');
            $sale->paid_amount = $paidAmount * 100;
            $sale->due_amount = $dueAmount * 100;
            $sale->status = $status;
            $sale->payment_status = $paymentStatus;
            $sale->payment_method = $overrides['payment_method'] ?? 'Cash';
            $sale->note = $overrides['note'] ?? $bonLivraison->getRawOriginal('note');
            $sale->save();

            foreach ($bonLivraison->bonLivraisonDetails as $detail) {
                SaleDetails::create([
                    'sale_id' => $sale->id,
                    'product_id' => $detail->getRawOriginal('product_id'),
                    'product_name' => $detail->getRawOriginal('product_name'),
                    'product_code' => $detail->getRawOriginal('product_code'),
                    'quantity' => $detail->getRawOriginal('quantity'),
                    'price' => $detail->getRawOriginal('price'),
                    'unit_price' => $detail->getRawOriginal('unit_price'),
                    'sub_total' => $detail->getRawOriginal('sub_total'),
                    'product_discount_amount' => $detail->getRawOriginal('product_discount_amount'),
                    'product_discount_type' => $detail->getRawOriginal('product_discount_type'),
                    'product_tax_amount' => $detail->getRawOriginal('product_tax_amount'),
                ]);

                if ($status === 'Shipped' || $status === 'Completed') {
                    $this->stock->stockOut(
                        Product::findOrFail($detail->product_id),
                        (int) $detail->quantity,
                        null,
                        'Sale',
                        $sale->id,
                    );
                }
            }

            if ($paidAmount > 0) {
                SalePayment::create([
                    'date' => $sale->date,
                    'reference' => 'INV/'.$sale->reference,
                    'amount' => $sale->paid_amount,
                    'sale_id' => $sale->id,
                    'payment_method' => $sale->payment_method,
                ]);
            }

            $bonLivraison->update(['status' => BonLivraison::STATUS_INVOICED]);

            if ($bonLivraison->commande_id !== null) {
                Commande::where('id', $bonLivraison->commande_id)
                    ->update(['status' => Commande::STATUS_INVOICED]);
            }

            return $sale->refresh();
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

            // Reduce the customer's outstanding credit balance by the payment.
            $customer = Customer::find($sale->customer_id);
            if ($customer !== null) {
                $this->credit->settle($customer, (int) round($data['amount'] * 100));
            }

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
