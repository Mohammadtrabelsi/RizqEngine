<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProductPricingModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate('create_products', 'web');
        Permission::findOrCreate('edit_products', 'web');
    }

    private function manager(): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['create_products', 'edit_products']);

        return $user;
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'product_name' => 'Test product',
            'product_code' => '12345678',
            'product_barcode_symbology' => 'EAN13',
            'product_unit' => 'pc',
            'product_quantity' => 5,
            'product_cost' => 100,
            'product_price' => 120,
            'product_stock_alert' => 10,
            'category_id' => Category::factory()->create()->id,
            'supplier_id' => Supplier::factory()->create()->id,
        ], $overrides);
    }

    public function test_sale_price_is_used_as_entered_in_price_mode(): void
    {
        $this->actingAs($this->manager())
            ->post(route('products.store'), $this->payload([
                'pricing_mode' => 'price',
                'product_price' => 150,
            ]))
            ->assertRedirect(route('products.index'));

        $this->assertSame(150.0, (float) Product::firstWhere('product_code', '12345678')->product_price);
    }

    public function test_sale_price_is_derived_from_cost_and_margin(): void
    {
        $this->actingAs($this->manager())
            ->post(route('products.store'), $this->payload([
                'pricing_mode' => 'margin',
                'product_cost' => 100,
                'product_margin' => 25,
                // A stale/browser-sent price must be overridden server-side.
                'product_price' => 999,
            ]))
            ->assertRedirect(route('products.index'));

        $product = Product::firstWhere('product_code', '12345678');

        $this->assertSame(125.0, (float) $product->product_price);
        // The margin field must not be persisted as a product column.
        $this->assertArrayNotHasKey('product_margin', $product->getAttributes());
    }

    public function test_margin_is_required_when_margin_mode_is_selected(): void
    {
        $this->actingAs($this->manager())
            ->post(route('products.store'), $this->payload([
                'pricing_mode' => 'margin',
                'product_margin' => null,
            ]))
            ->assertSessionHasErrors('product_margin');
    }
}
