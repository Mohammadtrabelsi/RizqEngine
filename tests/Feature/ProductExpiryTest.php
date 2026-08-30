<?php

namespace Tests\Feature;

use App\Livewire\Products\ProductIndex;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProductExpiryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate('access_products', 'web');
        Permission::findOrCreate('edit_products', 'web');
    }

    private function manager(): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['access_products', 'edit_products']);

        return $user;
    }

    public function test_is_expired_attribute_reflects_expiry_date(): void
    {
        $expired = Product::factory()->create(['expiry_date' => now()->subDay()->toDateString()]);
        $future = Product::factory()->create(['expiry_date' => now()->addDay()->toDateString()]);
        $none = Product::factory()->create(['expiry_date' => null]);

        $this->assertTrue($expired->is_expired);
        $this->assertFalse($future->is_expired);
        $this->assertFalse($none->is_expired);
    }

    public function test_expired_scope_only_returns_expired_products(): void
    {
        Product::factory()->expired()->count(2)->create();
        Product::factory()->create(['expiry_date' => now()->addWeek()->toDateString()]);
        Product::factory()->create(['expiry_date' => null]);

        $this->assertSame(2, Product::query()->expired()->count());
    }

    public function test_expiry_filter_narrows_the_listing(): void
    {
        $expired = Product::factory()->expired()->create();
        $fresh = Product::factory()->create(['expiry_date' => now()->addMonth()->toDateString()]);

        Livewire::actingAs($this->manager())
            ->test(ProductIndex::class)
            ->set('expiry', 'expired')
            ->assertSee($expired->product_code)
            ->assertDontSee($fresh->product_code);
    }

    public function test_mark_expired_out_of_stock_zeroes_expired_quantities(): void
    {
        $expired = Product::factory()->expired()->create(['product_quantity' => 15]);
        $fresh = Product::factory()->create([
            'expiry_date' => now()->addMonth()->toDateString(),
            'product_quantity' => 20,
        ]);

        Livewire::actingAs($this->manager())
            ->test(ProductIndex::class)
            ->call('markExpiredOutOfStock');

        $this->assertSame(0, $expired->fresh()->product_quantity);
        $this->assertSame(20, $fresh->fresh()->product_quantity);
    }

    public function test_mark_out_of_stock_helper_only_persists_when_stock_present(): void
    {
        $product = Product::factory()->create(['product_quantity' => 5]);
        $this->assertTrue($product->markOutOfStock());
        $this->assertSame(0, $product->fresh()->product_quantity);

        $empty = Product::factory()->create(['product_quantity' => 0]);
        $this->assertFalse($empty->markOutOfStock());
    }
}
