<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\SaleReturn;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'show_total_stats',
            'show_weekly_sales_purchases',
            'show_month_overview',
            'show_notifications',
        ] as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $this->seedSettings();
    }

    /**
     * The shared currency helper reads the single settings row, so a currency +
     * settings record must exist for any page that renders money to boot.
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

        // product_cost uses a mutator that stores the value in cents, so a
        // cost of 10 here means $10.00 per unit.
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

    /**
     * Amounts on sales/purchases are persisted as integer cents, so a
     * total_amount of 10000 represents $100.00.
     */
    protected function makeCompletedSale(Product $product, int $quantity, array $overrides = []): Sale
    {
        $sale = Sale::create(array_merge([
            'date' => Carbon::today()->toDateString(),
            'customer_name' => 'Walk-in',
            'tax_percentage' => 0,
            'tax_amount' => 0,
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'total_amount' => 10000,
            'paid_amount' => 6000,
            'due_amount' => 4000,
            'status' => 'Completed',
            'payment_status' => 'Partial',
            'payment_method' => 'Cash',
        ], $overrides));

        SaleDetails::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'product_name' => $product->product_name,
            'product_code' => $product->product_code,
            'quantity' => $quantity,
            'price' => 1500,
            'unit_price' => 1500,
            'sub_total' => 1500 * $quantity,
            'product_discount_amount' => 0,
            'product_discount_type' => 'fixed',
            'product_tax_amount' => 0,
        ]);

        return $sale;
    }

    /** @test */
    public function the_dashboard_requires_authentication()
    {
        $this->get(route('home'))->assertRedirect(route('login'));
    }

    /** @test */
    public function the_financial_summary_aggregates_revenue_cost_and_profit_for_the_range()
    {
        $product = $this->makeProduct(['product_cost' => 10]); // $10 per unit cost

        // In-range completed sale: $100 revenue, 2 units sold => $20 COGS.
        $this->makeCompletedSale($product, 2, [
            'date' => Carbon::today()->toDateString(),
            'total_amount' => 10000,
            'due_amount' => 4000,
        ]);

        // Out-of-range completed sale (last month) must not affect the
        // range-scoped revenue / cost of goods figures.
        $this->makeCompletedSale($product, 5, [
            'date' => Carbon::today()->subMonth()->toDateString(),
            'total_amount' => 50000,
            'due_amount' => 0,
        ]);

        // A completed sale return in range reduces revenue by $10.
        SaleReturn::create([
            'date' => Carbon::today()->toDateString(),
            'customer_name' => 'Walk-in',
            'tax_percentage' => 0,
            'tax_amount' => 0,
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'total_amount' => 1000,
            'paid_amount' => 1000,
            'due_amount' => 0,
            'status' => 'Completed',
            'payment_status' => 'Paid',
            'payment_method' => 'Cash',
        ]);

        $user = User::factory()->create();
        $user->givePermissionTo(['show_total_stats', 'show_notifications']);

        $from = Carbon::yesterday()->toDateString();
        $to = Carbon::today()->toDateString();

        $this->actingAs($user)
            ->get(route('home', ['from_date' => $from, 'to_date' => $to]))
            ->assertOk()
            ->assertViewHas('revenue', 90.0)          // 100 gross - 10 returns
            ->assertViewHas('cost_of_goods', 20.0)    // 2 units * $10 (out-of-range 5 units excluded)
            ->assertViewHas('profit', 70.0);          // 90 - 20
    }

    /** @test */
    public function receivables_and_payables_snapshot_outstanding_balances()
    {
        $product = $this->makeProduct();

        // Two completed sales carrying $40 due each => $80 receivable.
        $this->makeCompletedSale($product, 1, ['due_amount' => 4000]);
        $this->makeCompletedSale($product, 1, ['due_amount' => 4000]);

        // A completed purchase with $25 outstanding => $25 payable.
        Purchase::create([
            'date' => Carbon::today()->toDateString(),
            'supplier_name' => 'Acme',
            'tax_percentage' => 0,
            'tax_amount' => 0,
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'total_amount' => 5000,
            'paid_amount' => 2500,
            'due_amount' => 2500,
            'status' => 'Completed',
            'payment_status' => 'Partial',
            'payment_method' => 'Cash',
        ]);

        $user = User::factory()->create();
        $user->givePermissionTo(['show_total_stats', 'show_notifications']);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertViewHas('receivables', 80.0)
            ->assertViewHas('payables', 25.0);
    }
}
