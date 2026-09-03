<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockExit;
use App\Services\StockExitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers the "Bon de Sortie Provisoire / Sortie pour dépôt" (dépôt-vente) flow:
 * goods are consigned out, unsold goods are returned, and the sold portion
 * (Q_init - Q_retour) is invoiced automatically to the consignee.
 */
class ConsignmentStockExitTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(int $quantity = 0, float $price = 15): Product
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

    /** @test */
    public function a_consignment_return_restocks_the_unsold_and_invoices_the_sold(): void
    {
        $service = app(StockExitService::class);
        $product = $this->makeProduct(100, price: 20);
        $customer = $this->makeCustomer();

        // Émission: 30 units leave the main inventory into the dépôt.
        $exit = $service->createExit(
            ['date' => now()->toDateString(), 'kind' => 'consignment', 'customer_id' => $customer->id],
            [['product_id' => $product->id, 'quantity' => 30]],
        );

        $this->assertTrue($exit->isConsignment());
        $this->assertSame($customer->id, $exit->customer_id);
        $this->assertSame(70, $product->fresh()->product_quantity, 'stock leaves at emission');

        // Retour des invendus: 12 unsold come back → 18 were sold.
        $detail = $exit->details()->first();
        $entry = $service->createConsignmentReturn(
            $exit->fresh(),
            [['detail_id' => $detail->id, 'returned' => 12]],
        );

        // 12 restocked → 70 + 12 = 82.
        $this->assertSame(82, $product->fresh()->product_quantity);

        $detail->refresh();
        $this->assertSame(12, $detail->returned_quantity);
        $this->assertSame(18, $detail->sold_quantity);
        $this->assertSame(0, $detail->outstanding_quantity);

        // The exit is closed and an invoice was generated for the 18 sold units.
        $this->assertTrue($exit->fresh()->isClosed());
        $this->assertNotNull($entry->sale_id);

        $sale = Sale::findOrFail($entry->sale_id);
        $this->assertSame($customer->id, $sale->customer_id);
        $this->assertEquals(18 * 20, $sale->total_amount); // 360, accessor returns currency units
        $this->assertSame(18, $sale->saleDetails()->first()->quantity);
    }

    /** @test */
    public function a_full_return_closes_the_consignment_without_an_invoice(): void
    {
        $service = app(StockExitService::class);
        $product = $this->makeProduct(50);
        $customer = $this->makeCustomer();

        $exit = $service->createExit(
            ['date' => now()->toDateString(), 'kind' => 'consignment', 'customer_id' => $customer->id],
            [['product_id' => $product->id, 'quantity' => 10]],
        );

        $detail = $exit->details()->first();
        $entry = $service->createConsignmentReturn(
            $exit->fresh(),
            [['detail_id' => $detail->id, 'returned' => 10]],
        );

        $this->assertSame(50, $product->fresh()->product_quantity);
        $this->assertNull($entry->sale_id);
        $this->assertSame(0, Sale::count());
        $this->assertTrue($exit->fresh()->isClosed());
    }

    /** @test */
    public function it_refuses_a_return_larger_than_what_went_out(): void
    {
        $service = app(StockExitService::class);
        $product = $this->makeProduct(50);
        $customer = $this->makeCustomer();

        $exit = $service->createExit(
            ['date' => now()->toDateString(), 'kind' => 'consignment', 'customer_id' => $customer->id],
            [['product_id' => $product->id, 'quantity' => 10]],
        );

        $detail = $exit->details()->first();

        $this->expectException(\App\Exceptions\StockInconsistencyException::class);
        $service->createConsignmentReturn(
            $exit->fresh(),
            [['detail_id' => $detail->id, 'returned' => 11]],
        );
    }
}
