<?php

namespace Tests\Feature;

use App\Exceptions\CreditLimitExceededException;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\CustomerCreditService;
use App\Services\SaleService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCreditTest extends TestCase
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

    public function test_charge_increases_balance_within_limit(): void
    {
        $customer = Customer::factory()->create(['credit_limit' => 100_00, 'current_balance' => 0]);

        app(CustomerCreditService::class)->charge($customer, 40_00);

        $this->assertSame(40_00, (int) $customer->fresh()->current_balance);
    }

    public function test_charge_is_blocked_over_the_limit(): void
    {
        $customer = Customer::factory()->create(['credit_limit' => 50_00, 'current_balance' => 30_00]);

        $this->expectException(CreditLimitExceededException::class);

        app(CustomerCreditService::class)->charge($customer, 40_00);
    }

    public function test_credit_is_refused_when_no_limit_is_set(): void
    {
        $customer = Customer::factory()->create(['credit_limit' => 0]);
        $service = app(CustomerCreditService::class);

        $this->assertFalse($service->canCharge($customer, 10_00));

        $this->expectException(CreditLimitExceededException::class);
        $service->charge($customer, 10_00);
    }

    public function test_settle_reduces_the_balance(): void
    {
        $customer = Customer::factory()->create(['credit_limit' => 100_00, 'current_balance' => 60_00]);

        app(CustomerCreditService::class)->settle($customer, 25_00);

        $this->assertSame(35_00, (int) $customer->fresh()->current_balance);
    }

    public function test_available_credit_helper(): void
    {
        $customer = Customer::factory()->create(['credit_limit' => 100_00, 'current_balance' => 70_00]);

        $this->assertSame(30_00, $customer->availableCredit());
        $this->assertTrue($customer->allowsCredit());
    }

    public function test_pos_sale_on_credit_charges_the_customer(): void
    {
        $customer = Customer::factory()->create(['credit_limit' => 1000_00, 'current_balance' => 0]);
        $product = Product::factory()->create(['product_quantity' => 100, 'product_tax_type' => 3, 'product_order_tax' => 0]);
        $this->addCartItem($product, 2, 50);

        app(SaleService::class)->createPosSale([
            'customer_id' => $customer->id,
            'tax_percentage' => 0,
            'discount_percentage' => 0,
            'shipping_amount' => 0,
            'paid_amount' => 40,   // pay 40 of a 100 sale -> 60 on credit
            'total_amount' => 100,
            'payment_method' => 'Credit',
        ]);

        $this->assertSame(60_00, (int) $customer->fresh()->current_balance);
        $this->assertDatabaseCount('sales', 1);
    }

    public function test_pos_sale_is_blocked_when_it_exceeds_the_limit(): void
    {
        $customer = Customer::factory()->create(['credit_limit' => 50_00, 'current_balance' => 0]);
        $product = Product::factory()->create(['product_quantity' => 100, 'product_tax_type' => 3, 'product_order_tax' => 0]);
        $this->addCartItem($product, 2, 50);

        try {
            app(SaleService::class)->createPosSale([
                'customer_id' => $customer->id,
                'tax_percentage' => 0,
                'discount_percentage' => 0,
                'shipping_amount' => 0,
                'paid_amount' => 0,     // whole 100 on credit, limit is 50
                'total_amount' => 100,
                'payment_method' => 'Credit',
            ]);
            $this->fail('Expected CreditLimitExceededException.');
        } catch (CreditLimitExceededException) {
            // expected
        }

        // The whole sale rolled back: no sale, no stock removed, no balance.
        $this->assertDatabaseCount('sales', 0);
        $this->assertSame(100, (int) $product->fresh()->product_quantity);
        $this->assertSame(0, (int) $customer->fresh()->current_balance);
    }
}
