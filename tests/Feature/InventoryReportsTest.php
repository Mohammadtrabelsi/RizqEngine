<?php

namespace Tests\Feature;

use App\Livewire\Reports\InventoryValuationReport;
use App\Livewire\Reports\LowStockReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Currency\Entities\Currency;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\Product;
use Modules\Setting\Entities\Setting;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class InventoryReportsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate('access_reports', 'web');
        $this->seedSettings();
    }

    /**
     * The shared currency formatting helper reads the single settings row, so a
     * currency + settings record must exist for any page/component that renders
     * money to boot in the test environment.
     */
    protected function seedSettings(): void
    {
        $currency = Currency::create([
            'currency_name' => 'US Dollar',
            'code' => 'USD',
            'symbol' => '$',
            'thousand_separator' => ',',
            'decimal_separator' => '.',
            'exchange_rate' => 1,
        ]);

        Setting::create([
            'company_name' => 'Test Co',
            'company_email' => 'test@example.com',
            'company_phone' => '000',
            'default_currency_id' => $currency->id,
            'default_currency_position' => 'prefix',
            'notification_email' => 'test@example.com',
            'footer_text' => 'footer',
            'company_address' => 'address',
        ]);

        cache()->forget('settings');
    }

    protected function makeProduct(array $overrides = []): Product
    {
        $category = Category::firstOrCreate(
            ['category_code' => 'CAT'],
            ['category_name' => 'Category']
        );

        return Product::create(array_merge([
            'category_id' => $category->id,
            'product_name' => 'Widget',
            'product_code' => 'W-'.uniqid(),
            'product_quantity' => 10,
            'product_cost' => 10,
            'product_price' => 15,
            'product_stock_alert' => 5,
        ], $overrides));
    }

    /** @test */
    public function inventory_reports_require_the_reports_permission()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('inventory-valuation-report.index'))->assertForbidden();
        $this->actingAs($user)->get(route('low-stock-report.index'))->assertForbidden();
        $this->actingAs($user)->get(route('stock-movement-report.index'))->assertForbidden();
        $this->actingAs($user)->get(route('product-movement-report.index'))->assertForbidden();

        $user->givePermissionTo('access_reports');

        $this->actingAs($user)->get(route('inventory-valuation-report.index'))->assertOk();
        $this->actingAs($user)->get(route('low-stock-report.index'))->assertOk();
        $this->actingAs($user)->get(route('stock-movement-report.index'))->assertOk();
        $this->actingAs($user)->get(route('product-movement-report.index'))->assertOk();
    }

    /** @test */
    public function the_low_stock_report_lists_products_at_or_below_their_alert_level()
    {
        $healthy = $this->makeProduct(['product_quantity' => 50, 'product_stock_alert' => 5]);
        $low = $this->makeProduct(['product_quantity' => 3, 'product_stock_alert' => 5]);
        $out = $this->makeProduct(['product_quantity' => 0, 'product_stock_alert' => 5]);

        $component = Livewire::test(LowStockReport::class);

        $component->assertSee($low->product_name);
        $component->assertViewHas('lowStockCount', 2);
        $component->assertViewHas('outOfStockCount', 1);
    }

    /** @test */
    public function the_inventory_valuation_report_totals_stock_value()
    {
        // 10 units @ cost 10 => 100 cost value; @ price 15 => 150 retail value.
        $this->makeProduct(['product_quantity' => 10, 'product_cost' => 10, 'product_price' => 15]);

        Livewire::test(InventoryValuationReport::class)
            ->assertViewHas('totalQuantity', 10)
            ->assertViewHas('totalCostValue', 100.0)
            ->assertViewHas('totalRetailValue', 150.0)
            ->assertViewHas('potentialProfit', 50.0);
    }
}
