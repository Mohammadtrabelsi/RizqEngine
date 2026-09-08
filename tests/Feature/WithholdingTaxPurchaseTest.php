<?php

namespace Tests\Feature;

use App\Enums\WithholdingCalculationBase;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\WithholdingTax;
use App\Services\PurchaseService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithholdingTaxPurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Cart::instance('purchase')->destroy();

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
     * Seed the purchase cart with a single 1000 DT line and a 19% VAT so the
     * document totals are HT 1000 / TVA 190 / TTC 1190.
     */
    private function seedCart(Product $product): void
    {
        Cart::instance('purchase')->add([
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

        Cart::instance('purchase')->setGlobalTax(19);
    }

    private function baseData(Supplier $supplier, array $overrides = []): array
    {
        return array_merge([
            'date' => '2026-01-01',
            'supplier_id' => $supplier->id,
            'warehouse_id' => null,
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
        $supplier = Supplier::factory()->create();
        $product = $this->makeProduct();
        $withholding = WithholdingTax::factory()->create([
            'rate' => 3,
            'calculation_base' => WithholdingCalculationBase::TTC->value,
        ]);

        $this->seedCart($product);

        $purchase = app(PurchaseService::class)->createPurchase(
            $this->baseData($supplier, ['withholding_tax_ids' => [$withholding->id]]),
        );

        $this->assertEqualsWithDelta(35.700, $purchase->withholding_amount, 0.0001);
        $this->assertEqualsWithDelta(1154.300, $purchase->net_payable, 0.0001);
        // Nothing paid yet, so the supplier is owed the net.
        $this->assertEqualsWithDelta(1154.300, $purchase->due_amount, 0.0001);

        $line = $purchase->withholdingTaxes()->first();
        $this->assertEqualsWithDelta(35.700, $line->amount, 0.0001);
        $this->assertEqualsWithDelta(1190.0, $line->taxable_amount, 0.0001);
        $this->assertSame(3.0, (float) $line->rate);
    }

    public function test_no_withholding_leaves_net_equal_to_ttc(): void
    {
        $supplier = Supplier::factory()->create();
        $product = $this->makeProduct();

        $this->seedCart($product);

        $purchase = app(PurchaseService::class)->createPurchase($this->baseData($supplier));

        $this->assertSame(0.0, (float) $purchase->withholding_amount);
        $this->assertEqualsWithDelta(1190.0, $purchase->net_payable, 0.0001);
        $this->assertEqualsWithDelta(1190.0, $purchase->due_amount, 0.0001);
        $this->assertCount(0, $purchase->withholdingTaxes);
    }

    public function test_multiple_withholdings_are_stored_as_separate_lines(): void
    {
        $supplier = Supplier::factory()->create();
        $product = $this->makeProduct();
        $wht1 = WithholdingTax::factory()->create(['rate' => 3, 'calculation_base' => WithholdingCalculationBase::TTC->value]);
        $wht2 = WithholdingTax::factory()->create(['rate' => 1, 'calculation_base' => WithholdingCalculationBase::HT->value]);

        $this->seedCart($product);

        $purchase = app(PurchaseService::class)->createPurchase(
            $this->baseData($supplier, ['withholding_tax_ids' => [$wht1->id, $wht2->id]]),
        );

        $this->assertCount(2, $purchase->withholdingTaxes);
        // 35.700 + 10.000 = 45.700
        $this->assertEqualsWithDelta(45.700, $purchase->withholding_amount, 0.0001);
    }

    public function test_changing_master_rate_does_not_alter_historical_snapshot(): void
    {
        $supplier = Supplier::factory()->create();
        $product = $this->makeProduct();
        $withholding = WithholdingTax::factory()->create([
            'rate' => 3,
            'calculation_base' => WithholdingCalculationBase::TTC->value,
        ]);

        $this->seedCart($product);

        $purchase = app(PurchaseService::class)->createPurchase(
            $this->baseData($supplier, ['withholding_tax_ids' => [$withholding->id]]),
        );

        // Administrator later raises the rate.
        $withholding->update(['rate' => 10]);

        $line = $purchase->fresh()->withholdingTaxes()->first();
        $this->assertSame(3.0, (float) $line->rate);
        $this->assertEqualsWithDelta(35.700, $line->amount, 0.0001);
        $this->assertEqualsWithDelta(35.700, $purchase->fresh()->withholding_amount, 0.0001);
    }
}
