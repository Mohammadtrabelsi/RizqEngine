<?php

namespace Tests\Feature;

use App\Exceptions\ConversionException;
use App\Models\Category;
use App\Models\Commande;
use App\Models\CommandeDetails;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StockExit;
use App\Services\StockExitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers generating a consignment Bon de Sortie from a Commande: the order's
 * lines leave inventory into the dépôt for the order's customer, and the sold
 * portion is invoiced later through the normal Bon d'Entrée régularisation.
 */
class CommandeToStockExitTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(int $quantity = 100, float $price = 20): Product
    {
        $category = Category::create([
            'category_code' => 'CAT',
            'category_name' => 'Category',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'product_name' => 'Widget',
            'product_code' => 'W-'.uniqid(),
            'product_quantity' => $quantity,
            'product_cost' => 10,
            'product_price' => $price,
            'product_stock_alert' => 5,
        ]);
    }

    private function makeCustomer(): Customer
    {
        return Customer::create([
            'customer_name' => 'Reseller',
            'customer_email' => 'reseller@example.test',
            'customer_phone' => '0000',
            'city' => 'City',
            'country' => 'Country',
            'address' => 'Address',
        ]);
    }

    private function makeCommande(Customer $customer, Product $product, int $quantity = 30): Commande
    {
        $commande = Commande::create([
            'date' => now()->toDateString(),
            'customer_id' => $customer->id,
            'customer_name' => $customer->customer_name,
            'status' => Commande::STATUS_CONFIRMED,
            'tax_percentage' => 0,
            'discount_percentage' => 0,
            'shipping_amount' => 0,
            'total_amount' => 0,
            'tax_amount' => 0,
            'discount_amount' => 0,
        ]);

        CommandeDetails::create([
            'commande_id' => $commande->id,
            'product_id' => $product->id,
            'product_name' => $product->product_name,
            'product_code' => $product->product_code,
            'quantity' => $quantity,
            'price' => 20 * 100,
            'unit_price' => 20 * 100,
            'sub_total' => $quantity * 20 * 100,
            'product_discount_amount' => 0,
            'product_discount_type' => 'fixed',
            'product_tax_amount' => 0,
        ]);

        return $commande->refresh();
    }

    /** @test */
    public function it_generates_a_consignment_exit_from_a_commande_and_destocks(): void
    {
        $service = app(StockExitService::class);
        $product = $this->makeProduct(100);
        $customer = $this->makeCustomer();
        $commande = $this->makeCommande($customer, $product, 30);

        $exit = $service->createFromCommande($commande);

        $this->assertTrue($exit->isConsignment());
        $this->assertSame($customer->id, $exit->customer_id);
        $this->assertSame($commande->id, $exit->commande_id);
        $this->assertSame($exit->id, $commande->fresh()->stockExits()->first()->id);
        $this->assertTrue($commande->fresh()->hasStockExit());

        // 30 units left the main inventory into the dépôt.
        $this->assertSame(70, $product->fresh()->product_quantity);

        $detail = $exit->details()->first();
        $this->assertSame($product->id, $detail->product_id);
        $this->assertSame(30, $detail->quantity);
    }

    /** @test */
    public function it_refuses_to_generate_a_second_exit_for_the_same_commande(): void
    {
        $service = app(StockExitService::class);
        $product = $this->makeProduct(100);
        $customer = $this->makeCustomer();
        $commande = $this->makeCommande($customer, $product, 10);

        $service->createFromCommande($commande);

        $this->expectException(ConversionException::class);
        $service->createFromCommande($commande->fresh());
    }

    /** @test */
    public function the_generated_exit_can_be_regularised_updating_stock(): void
    {
        $service = app(StockExitService::class);
        $product = $this->makeProduct(100);
        $customer = $this->makeCustomer();
        $commande = $this->makeCommande($customer, $product, 30);

        $exit = $service->createFromCommande($commande);
        $detail = $exit->details()->first();

        // 12 unsold returned → 18 sold; unsold restocked to 70 + 12 = 82.
        $entry = $service->createConsignmentReturn(
            $exit->fresh(),
            [['detail_id' => $detail->id, 'returned' => 12]],
        );

        $this->assertSame(82, $product->fresh()->product_quantity);
        $this->assertTrue(StockExit::find($exit->id)->isClosed());
        $this->assertNotNull($entry->sale_id);
    }
}
