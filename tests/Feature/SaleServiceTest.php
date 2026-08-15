<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientStockException;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Cart::instance('sale')->destroy();

        parent::tearDown();
    }

    private function addCartItem(Product $product, int $qty, float $price): void
    {
        Cart::instance('sale')->add([
            'id' => $product->id,
            'name' => 'Widget',
            'qty' => $qty,
            'price' => $price,
            'weight' => 1,
            'options' => [
                'product_discount' => 0.00,
                'product_discount_type' => 'fixed',
                'sub_total' => $price,
                'code' => $product->product_code,
                'stock' => $product->product_quantity,
                'unit' => 'PC',
                'product_tax' => 0.00,
                'unit_price' => $price,
            ],
        ]);
    }

    /** @test */
    public function creating_a_completed_sale_deducts_stock_and_records_an_attributed_movement()
    {
        $product = Product::factory()->create([
            'product_quantity' => 20,
            'product_tax_type' => 3,
            'product_order_tax' => 0,
        ]);
        $customer = Customer::factory()->create();

        $this->addCartItem($product, 5, 10);

        $sale = app(SaleService::class)->createSale([
            'date' => '2026-01-01',
            'customer_id' => $customer->id,
            'tax_percentage' => 0,
            'discount_percentage' => 0,
            'shipping_amount' => 0,
            'paid_amount' => 50,
            'total_amount' => 50,
            'status' => 'Completed',
            'payment_method' => 'Cash',
            'note' => null,
        ]);

        // Sale is fully paid (due == 0).
        $this->assertSame('Paid', $sale->payment_status);
        $this->assertSame('Completed', $sale->status);

        // Stock is deducted through StockService.
        $this->assertEquals(15, $product->fresh()->product_quantity);

        // The stock movement is attributed to the sale, not left anonymous.
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 5,
            'reference_type' => 'Sale',
            'reference_id' => $sale->id,
        ]);

        $this->assertDatabaseHas('sale_details', [
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        // A payment row is created because paid_amount > 0.
        $this->assertDatabaseHas('sale_payments', [
            'sale_id' => $sale->id,
        ]);
    }

    /** @test */
    public function overselling_a_completed_sale_is_rejected_and_rolled_back()
    {
        $product = Product::factory()->create([
            'product_quantity' => 3,
            'product_tax_type' => 3,
            'product_order_tax' => 0,
        ]);
        $customer = Customer::factory()->create();

        $this->addCartItem($product, 10, 10);

        try {
            app(SaleService::class)->createSale([
                'date' => '2026-01-01',
                'customer_id' => $customer->id,
                'tax_percentage' => 0,
                'discount_percentage' => 0,
                'shipping_amount' => 0,
                'paid_amount' => 0,
                'total_amount' => 100,
                'status' => 'Completed',
                'payment_method' => 'Cash',
                'note' => null,
            ]);
            $this->fail('Expected InsufficientStockException was not thrown.');
        } catch (InsufficientStockException $e) {
            // expected
        }

        // The whole transaction rolled back: no sale, no stock change.
        $this->assertSame(0, Sale::count());
        $this->assertEquals(3, $product->fresh()->product_quantity);
    }

    /** @test */
    public function a_draft_sale_does_not_touch_stock()
    {
        $product = Product::factory()->create([
            'product_quantity' => 20,
            'product_tax_type' => 3,
            'product_order_tax' => 0,
        ]);
        $customer = Customer::factory()->create();

        $this->addCartItem($product, 5, 10);

        app(SaleService::class)->createSale([
            'date' => '2026-01-01',
            'customer_id' => $customer->id,
            'tax_percentage' => 0,
            'discount_percentage' => 0,
            'shipping_amount' => 0,
            'paid_amount' => 0,
            'total_amount' => 50,
            'status' => 'Pending',
            'payment_method' => 'Cash',
            'note' => null,
        ]);

        $this->assertEquals(20, $product->fresh()->product_quantity);
    }

    /** @test */
    public function recording_a_partial_payment_updates_the_sale_status()
    {
        $product = Product::factory()->create([
            'product_quantity' => 20,
            'product_tax_type' => 3,
            'product_order_tax' => 0,
        ]);
        $customer = Customer::factory()->create();

        $this->addCartItem($product, 1, 100);

        $sale = app(SaleService::class)->createSale([
            'date' => '2026-01-01',
            'customer_id' => $customer->id,
            'tax_percentage' => 0,
            'discount_percentage' => 0,
            'shipping_amount' => 0,
            'paid_amount' => 0,
            'total_amount' => 100,
            'status' => 'Completed',
            'payment_method' => 'Cash',
            'note' => null,
        ]);

        $this->assertSame('Unpaid', $sale->payment_status);

        app(SaleService::class)->addPayment([
            'date' => '2026-01-02',
            'reference' => 'PAY-1',
            'amount' => 40,
            'note' => null,
            'sale_id' => $sale->id,
            'payment_method' => 'Cash',
        ]);

        $sale->refresh();

        $this->assertSame('Partial', $sale->payment_status);
        $this->assertEquals(40, $sale->paid_amount);
        $this->assertEquals(60, $sale->due_amount);
    }
}
