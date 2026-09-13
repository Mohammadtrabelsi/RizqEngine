<?php

namespace Tests\Feature;

use App\Enums\WithholdingCalculationBase;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\WithholdingTax;
use App\Services\SaleService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithholdingTaxSaleTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Cart::instance('sale')->destroy();

        parent::tearDown();
    }

    private function makeProduct(): Product
    {
        $category = Category::create([
            'category_code' => 'CAT',
            'category_name' => 'Category',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'product_name' => 'Widget',
            'product_code' => 'W-'.uniqid(),
            'product_quantity' => 100,
            'product_cost' => 10,
            'product_price' => 15,
            'product_stock_alert' => 5,
        ]);
    }

    /**
     * Seed the sale cart with a single 1000 DT line and a 19% VAT so the
     * document totals are HT 1000 / TVA 190 / TTC 1190.
     */
    private function seedCart(Product $product): void
    {
        Cart::instance('sale')->add([
            'id' => $product->id,
            'name' => 'Widget',
            'qty' => 1,
            'price' => 1000,
            'weight' => 1,
            'options' => [
                'product_discount' => 0.00,
                'product_discount_type' => 'fixed',
                'sub_total' => 1000,
                'code' => $product->product_code,
                'stock' => $product->product_quantity,
                'unit' => 'PC',
                'product_tax' => 190.00,
                'unit_price' => 1000,
            ],
        ]);

        Cart::instance('sale')->setGlobalTax(19);
    }

    private function baseData(Customer $customer, array $overrides = []): array
    {
        return array_merge([
            'date' => '2026-01-01',
            'customer_id' => $customer->id,
            'tax_percentage' => 19,
            'discount_percentage' => 0,
            'shipping_amount' => 0,
            'paid_amount' => 0,
            'total_amount' => 1190,
            'status' => 'Pending',
            'payment_method' => 'Cash',
            'note' => null,
        ], $overrides);
    }

    public function test_ttc_withholding_sets_net_payable_and_due(): void
    {
        $customer = Customer::factory()->create();
        $product = $this->makeProduct();
        $withholding = WithholdingTax::factory()->forSales()->create([
            'rate' => 3,
            'calculation_base' => WithholdingCalculationBase::TTC->value,
        ]);

        $this->seedCart($product);

        $sale = app(SaleService::class)->createSale(
            $this->baseData($customer, ['withholding_tax_ids' => [$withholding->id]]),
        );

        $this->assertEqualsWithDelta(35.700, $sale->withholding_amount, 0.0001);
        $this->assertEqualsWithDelta(1154.300, $sale->net_payable, 0.0001);
        // Nothing paid yet, so the customer owes the net.
        $this->assertEqualsWithDelta(1154.300, $sale->due_amount, 0.0001);

        $line = $sale->withholdingTaxes()->first();
        $this->assertEqualsWithDelta(35.700, $line->amount, 0.0001);
        $this->assertEqualsWithDelta(1190.0, $line->taxable_amount, 0.0001);
        $this->assertSame(3.0, (float) $line->rate);
    }

    public function test_no_withholding_leaves_net_equal_to_ttc(): void
    {
        $customer = Customer::factory()->create();
        $product = $this->makeProduct();

        $this->seedCart($product);

        $sale = app(SaleService::class)->createSale($this->baseData($customer));

        $this->assertSame(0.0, (float) $sale->withholding_amount);
        $this->assertEqualsWithDelta(1190.0, $sale->net_payable, 0.0001);
        $this->assertEqualsWithDelta(1190.0, $sale->due_amount, 0.0001);
        $this->assertCount(0, $sale->withholdingTaxes);
    }

    public function test_changing_master_rate_does_not_alter_historical_snapshot(): void
    {
        $customer = Customer::factory()->create();
        $product = $this->makeProduct();
        $withholding = WithholdingTax::factory()->forSales()->create([
            'rate' => 3,
            'calculation_base' => WithholdingCalculationBase::TTC->value,
        ]);

        $this->seedCart($product);

        $sale = app(SaleService::class)->createSale(
            $this->baseData($customer, ['withholding_tax_ids' => [$withholding->id]]),
        );

        // Administrator later raises the rate.
        $withholding->update(['rate' => 10]);

        $line = $sale->fresh()->withholdingTaxes()->first();
        $this->assertSame(3.0, (float) $line->rate);
        $this->assertEqualsWithDelta(35.700, $line->amount, 0.0001);
        $this->assertEqualsWithDelta(35.700, $sale->fresh()->withholding_amount, 0.0001);
    }
}
