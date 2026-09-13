<?php

namespace Tests\Feature;

use App\Livewire\Customers\CustomerPriceList;
use App\Livewire\ProductCart;
use App\Models\Customer;
use App\Models\CustomerProductPrice;
use App\Models\Product;
use App\Models\User;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CustomerPriceListTest extends TestCase
{
    use RefreshDatabase;

    private function actingWith(string ...$permissions): User
    {
        $user = User::factory()->create();

        foreach ($permissions as $permission) {
            $user->givePermissionTo(Permission::findOrCreate($permission, 'web'));
        }

        $this->actingAs($user);

        return $user;
    }

    protected function tearDown(): void
    {
        Cart::instance('sale')->destroy();

        parent::tearDown();
    }

    public function test_price_for_returns_negotiated_price_or_null(): void
    {
        $customer = Customer::factory()->create();
        $withPrice = Product::factory()->create();
        $withoutPrice = Product::factory()->create();

        CustomerProductPrice::factory()->create([
            'customer_id' => $customer->id,
            'product_id' => $withPrice->id,
            'price' => 42,
        ]);

        $this->assertSame(42, $customer->priceFor($withPrice));
        $this->assertNull($customer->priceFor($withoutPrice));
    }

    public function test_management_component_creates_and_deletes_prices(): void
    {
        $this->actingWith('edit_customers');

        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        Livewire::test(CustomerPriceList::class, ['customer' => $customer])
            ->set('product_id', $product->id)
            ->set('price', 150)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('customer_product_prices', [
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'price' => 150,
        ]);

        $line = CustomerProductPrice::where('customer_id', $customer->id)->firstOrFail();

        Livewire::test(CustomerPriceList::class, ['customer' => $customer])
            ->call('delete', $line->id);

        $this->assertDatabaseMissing('customer_product_prices', ['id' => $line->id]);
    }

    public function test_duplicate_price_for_same_product_is_rejected(): void
    {
        $this->actingWith('edit_customers');

        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        CustomerProductPrice::factory()->create([
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'price' => 10,
        ]);

        Livewire::test(CustomerPriceList::class, ['customer' => $customer])
            ->set('product_id', $product->id)
            ->set('price', 99)
            ->call('save')
            ->assertHasErrors('product_id');
    }

    public function test_cart_uses_customer_price_when_customer_selected(): void
    {
        Cart::instance('sale')->destroy();

        $customer = Customer::factory()->create();
        // No-tax product so the cart price equals the base price directly.
        $product = Product::factory()->create([
            'product_price' => 100,
            'product_tax_type' => 0,
            'product_order_tax' => 0,
        ]);

        CustomerProductPrice::factory()->create([
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'price' => 70,
        ]);

        Livewire::test(ProductCart::class, ['cartInstance' => 'sale'])
            ->call('customerSelected', $customer->id)
            ->call('productSelected', $product->toArray());

        $item = Cart::instance('sale')->content()->first();

        $this->assertNotNull($item);
        $this->assertEquals(70, $item->price);
    }

    public function test_cart_uses_default_price_without_customer_price(): void
    {
        Cart::instance('sale')->destroy();

        $customer = Customer::factory()->create();
        $product = Product::factory()->create([
            'product_price' => 100,
            'product_tax_type' => 0,
            'product_order_tax' => 0,
        ]);

        Livewire::test(ProductCart::class, ['cartInstance' => 'sale'])
            ->call('customerSelected', $customer->id)
            ->call('productSelected', $product->toArray());

        $item = Cart::instance('sale')->content()->first();

        $this->assertNotNull($item);
        $this->assertEquals(100, $item->price);
    }
}
