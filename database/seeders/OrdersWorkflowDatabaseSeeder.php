<?php

namespace Database\Seeders;

use App\Models\BonCommande;
use App\Models\BonCommandeDetails;
use App\Models\BonLivraison;
use App\Models\BonLivraisonDetails;
use App\Models\Commande;
use App\Models\CommandeDetails;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Demo transactional data covering the full customer sales pipeline
 * (Devis → Bon de Commande → Commande → Bon de Livraison) and supplier
 * purchase orders. Every document status — including the partial-shipping
 * states — is represented so the workflow screens can be explored end to end.
 *
 * Depends on the catalogue and address book seeded by
 * ProductDatabaseSeeder, CustomerDatabaseSeeder and SupplierDatabaseSeeder.
 */
class OrdersWorkflowDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Idempotent: skip entirely if demo orders were already seeded so the
        // seeder can be re-run without piling up duplicate documents.
        if (Commande::query()->exists() || BonCommande::query()->exists()) {
            return;
        }

        $customers = Customer::orderBy('id')->get();
        $products = Product::orderBy('id')->get();
        $suppliers = Supplier::orderBy('id')->get();

        if ($customers->isEmpty() || $products->count() < 2) {
            return;
        }

        $this->seedCustomerPipeline($customers, $products);

        if ($suppliers->isNotEmpty()) {
            $this->seedSupplierPurchases($suppliers, $products);
        }
    }

    /**
     * Build the integer-cent line items for a set of products and quantities,
     * plus the order-header totals derived from them. No per-line discounts or
     * taxes are applied so the totals stay easy to read in demo data.
     *
     * @param  array<int, array{product: Product, quantity: int}>  $picks
     * @return array{lines: array<int, array<string, mixed>>, total: int}
     */
    private function buildLines(array $picks): array
    {
        $lines = [];
        $total = 0;

        foreach ($picks as $pick) {
            $product = $pick['product'];
            $quantity = $pick['quantity'];
            $unitPrice = (int) round($product->product_price * 100);
            $subTotal = $unitPrice * $quantity;
            $total += $subTotal;

            $lines[] = [
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
            ];
        }

        return ['lines' => $lines, 'total' => $total];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Customer>  $customers
     * @param  \Illuminate\Support\Collection<int, Product>  $products
     */
    private function seedCustomerPipeline($customers, $products): void
    {
        $c0 = $customers[0];
        $c1 = $customers[1] ?? $customers[0];
        $c2 = $customers[2] ?? $customers[0];

        $p = fn (int $i) => $products[$i % $products->count()];

        // ---- Bons de Commande: draft / confirmed / cancelled ----
        // (a fourth, "converted", is produced further down when a Commande is
        // created from a Bon de Commande.)
        $this->makeBonCommande($c0, [
            ['product' => $p(0), 'quantity' => 5],
            ['product' => $p(1), 'quantity' => 3],
        ], BonCommande::STATUS_DRAFT, now()->subDays(20));

        $this->makeBonCommande($c1, [
            ['product' => $p(2), 'quantity' => 10],
        ], BonCommande::STATUS_CONFIRMED, now()->subDays(18));

        $this->makeBonCommande($c2, [
            ['product' => $p(3), 'quantity' => 2],
            ['product' => $p(4), 'quantity' => 4],
        ], BonCommande::STATUS_CANCELLED, now()->subDays(16));

        // ---- A confirmed Bon de Commande converted into a Commande ----
        $convertedBc = $this->makeBonCommande($c0, [
            ['product' => $p(0), 'quantity' => 8],
            ['product' => $p(2), 'quantity' => 6],
        ], BonCommande::STATUS_CONVERTED, now()->subDays(14));

        $linkedCommande = $this->makeCommande($c0, [
            ['product' => $p(0), 'quantity' => 8],
            ['product' => $p(2), 'quantity' => 6],
        ], Commande::STATUS_CONFIRMED, Commande::SHIPPING_NOT_SHIPPED, now()->subDays(13), $convertedBc->id);

        // ---- Commandes covering every status × shipping-status of interest ----

        // Pending, nothing shipped yet.
        $this->makeCommande($c1, [
            ['product' => $p(1), 'quantity' => 4],
            ['product' => $p(5), 'quantity' => 5],
        ], Commande::STATUS_PENDING, Commande::SHIPPING_NOT_SHIPPED, now()->subDays(12));

        // Confirmed and partially shipped: one Bon de Livraison ships part of
        // the ordered quantities.
        $partialCommande = $this->makeCommande($c2, [
            ['product' => $p(0), 'quantity' => 10],
            ['product' => $p(3), 'quantity' => 6],
        ], Commande::STATUS_CONFIRMED, Commande::SHIPPING_PARTIALLY_SHIPPED, now()->subDays(10));

        $this->makeBonLivraisonFromCommande(
            $partialCommande,
            [$partialCommande->commandeDetails[0]->id => 4, $partialCommande->commandeDetails[1]->id => 2],
            BonLivraison::STATUS_DELIVERED,
            now()->subDays(9)
        );

        // Confirmed and fully shipped across two Bons de Livraison.
        $fullCommande = $this->makeCommande($c0, [
            ['product' => $p(2), 'quantity' => 6],
        ], Commande::STATUS_CONFIRMED, Commande::SHIPPING_SHIPPED, now()->subDays(8));

        $this->makeBonLivraisonFromCommande(
            $fullCommande,
            [$fullCommande->commandeDetails[0]->id => 4],
            BonLivraison::STATUS_DELIVERED,
            now()->subDays(7)
        );
        $this->makeBonLivraisonFromCommande(
            $fullCommande,
            [$fullCommande->commandeDetails[0]->id => 2],
            BonLivraison::STATUS_PENDING,
            now()->subDays(6)
        );

        // Invoiced Commande, fully shipped and its delivery note invoiced too.
        $invoicedCommande = $this->makeCommande($c1, [
            ['product' => $p(4), 'quantity' => 3],
            ['product' => $p(1), 'quantity' => 2],
        ], Commande::STATUS_INVOICED, Commande::SHIPPING_SHIPPED, now()->subDays(5));

        $this->makeBonLivraisonFromCommande(
            $invoicedCommande,
            [
                $invoicedCommande->commandeDetails[0]->id => 3,
                $invoicedCommande->commandeDetails[1]->id => 2,
            ],
            BonLivraison::STATUS_INVOICED,
            now()->subDays(4)
        );

        // Keep the converted-chain Commande referenced so static analysers do
        // not flag it as unused; its Bon de Commande already covers the
        // "converted" state above.
        unset($linkedCommande);
    }

    /**
     * @param  array<int, array{product: Product, quantity: int}>  $picks
     */
    private function makeBonCommande(Customer $customer, array $picks, string $status, Carbon $date): BonCommande
    {
        $built = $this->buildLines($picks);

        $bc = BonCommande::create([
            'date' => $date->format('Y-m-d'),
            'customer_id' => $customer->id,
            'customer_name' => $customer->customer_name,
            'tax_percentage' => 0,
            'tax_amount' => 0,
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'total_amount' => $built['total'],
            'status' => $status,
            'note' => 'Demo Bon de Commande ('.$status.').',
        ]);

        foreach ($built['lines'] as $line) {
            BonCommandeDetails::create(['bon_commande_id' => $bc->id] + $line);
        }

        return $bc;
    }

    /**
     * @param  array<int, array{product: Product, quantity: int}>  $picks
     */
    private function makeCommande(Customer $customer, array $picks, string $status, string $shippingStatus, Carbon $date, ?int $bonCommandeId = null): Commande
    {
        $built = $this->buildLines($picks);

        $commande = Commande::create([
            'date' => $date->format('Y-m-d'),
            'bon_commande_id' => $bonCommandeId,
            'customer_id' => $customer->id,
            'customer_name' => $customer->customer_name,
            'tax_percentage' => 0,
            'tax_amount' => 0,
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'total_amount' => $built['total'],
            'status' => $status,
            'shipping_status' => $shippingStatus,
            'note' => 'Demo Commande ('.$status.' / '.$shippingStatus.').',
        ]);

        foreach ($built['lines'] as $line) {
            CommandeDetails::create(['commande_id' => $commande->id] + $line);
        }

        return $commande->load('commandeDetails');
    }

    /**
     * Ship a subset of a Commande's lines onto a new Bon de Livraison.
     *
     * @param  array<int, int>  $shipByDetailId  commande_detail_id => quantity to ship
     */
    private function makeBonLivraisonFromCommande(Commande $commande, array $shipByDetailId, string $status, Carbon $date): BonLivraison
    {
        $details = $commande->commandeDetails->keyBy('id');
        $total = 0;
        $lines = [];

        foreach ($shipByDetailId as $detailId => $shipQty) {
            $detail = $details[$detailId];
            $unitPrice = (int) $detail->getRawOriginal('unit_price');
            $subTotal = $unitPrice * $shipQty;
            $total += $subTotal;

            $lines[] = [
                'commande_detail_id' => $detail->id,
                'product_id' => $detail->product_id,
                'product_name' => $detail->product_name,
                'product_code' => $detail->product_code,
                'quantity' => $shipQty,
                'price' => $unitPrice,
                'unit_price' => $unitPrice,
                'sub_total' => $subTotal,
                'product_discount_amount' => 0,
                'product_discount_type' => 'fixed',
                'product_tax_amount' => 0,
            ];
        }

        $bl = BonLivraison::create([
            'date' => $date->format('Y-m-d'),
            'commande_id' => $commande->id,
            'customer_id' => $commande->customer_id,
            'customer_name' => $commande->customer_name,
            'tax_percentage' => 0,
            'tax_amount' => 0,
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'total_amount' => $total,
            'status' => $status,
            'note' => 'Demo Bon de Livraison ('.$status.').',
        ]);

        foreach ($lines as $line) {
            BonLivraisonDetails::create(['bon_livraison_id' => $bl->id] + $line);
        }

        return $bl;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Supplier>  $suppliers
     * @param  \Illuminate\Support\Collection<int, Product>  $products
     */
    private function seedSupplierPurchases($suppliers, $products): void
    {
        if (Purchase::query()->exists()) {
            return;
        }

        $p = fn (int $i) => $products[$i % $products->count()];

        // status × payment_status combinations covering the supplier order flow.
        $orders = [
            ['status' => 'Pending', 'payment_status' => 'Unpaid', 'paid_ratio' => 0.0, 'days' => 22],
            ['status' => 'Ordered', 'payment_status' => 'Partial', 'paid_ratio' => 0.5, 'days' => 18],
            ['status' => 'Completed', 'payment_status' => 'Paid', 'paid_ratio' => 1.0, 'days' => 12],
            ['status' => 'Completed', 'payment_status' => 'Partial', 'paid_ratio' => 0.4, 'days' => 6],
        ];

        foreach ($orders as $index => $order) {
            $supplier = $suppliers[$index % $suppliers->count()];

            $picks = [
                ['product' => $p($index), 'quantity' => 20 + $index * 5],
                ['product' => $p($index + 1), 'quantity' => 10 + $index * 3],
            ];
            $built = $this->buildLines($picks);

            $total = $built['total'];
            $paid = (int) round($total * $order['paid_ratio']);
            $due = $total - $paid;

            $purchase = Purchase::create([
                'date' => now()->subDays($order['days'])->format('Y-m-d'),
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->supplier_name,
                'tax_percentage' => 0,
                'tax_amount' => 0,
                'discount_percentage' => 0,
                'discount_amount' => 0,
                'shipping_amount' => 0,
                'total_amount' => $total,
                'paid_amount' => $paid,
                'due_amount' => $due,
                'status' => $order['status'],
                'payment_status' => $order['payment_status'],
                'payment_method' => 'Cash',
                'note' => 'Demo supplier order ('.$order['status'].' / '.$order['payment_status'].').',
            ]);

            foreach ($built['lines'] as $line) {
                PurchaseDetail::create(['purchase_id' => $purchase->id] + $line);
            }
        }
    }
}
