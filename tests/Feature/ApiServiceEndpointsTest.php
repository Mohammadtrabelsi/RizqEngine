<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke tests for the API controllers that expose the service layer. Each
 * assertion exercises the route -> controller -> service wiring for a
 * representative service method.
 */
class ApiServiceEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsApiUser(): User
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'api');

        return $user;
    }

    private function makeProduct(array $overrides = []): Product
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
    public function api_endpoints_require_authentication(): void
    {
        $this->getJson('/api/categories')->assertUnauthorized();
    }

    /** @test */
    public function it_runs_the_category_crud_through_the_service(): void
    {
        $this->actingAsApiUser();

        // next-code helper
        $this->getJson('/api/categories/next-code')
            ->assertOk()
            ->assertJsonStructure(['next_code']);

        // create
        $created = $this->postJson('/api/categories', [
            'category_code' => 'CA_99',
            'category_name' => 'Beverages',
        ])->assertCreated()->json();

        $this->assertDatabaseHas('categories', ['category_code' => 'CA_99']);

        // index
        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonStructure(['data', 'current_page', 'total']);

        // show
        $this->getJson('/api/categories/'.$created['id'])
            ->assertOk()
            ->assertJsonPath('id', $created['id']);

        // update
        $this->putJson('/api/categories/'.$created['id'], [
            'category_code' => 'CA_99',
            'category_name' => 'Drinks',
        ])->assertOk();

        // category_name is translatable, so it is persisted as a JSON blob of
        // locale => value; assert against the resolved translation rather than
        // the raw column.
        $this->assertSame('Drinks', Category::findOrFail($created['id'])->getTranslation('category_name', 'en'));

        // destroy
        $this->deleteJson('/api/categories/'.$created['id'])
            ->assertOk()
            ->assertJsonPath('deleted', true);

        $this->assertDatabaseMissing('categories', ['id' => $created['id']]);
    }

    /** @test */
    public function it_refuses_to_delete_a_category_with_products(): void
    {
        $this->actingAsApiUser();
        $product = $this->makeProduct();

        $this->deleteJson('/api/categories/'.$product->category_id)
            ->assertStatus(422);

        $this->assertDatabaseHas('categories', ['id' => $product->category_id]);
    }

    /** @test */
    public function it_moves_stock_through_the_stock_service(): void
    {
        $this->actingAsApiUser();
        $product = $this->makeProduct(['product_quantity' => 10]);

        $this->postJson("/api/products/{$product->id}/stock-in", ['quantity' => 5])
            ->assertOk()
            ->assertJsonPath('product_quantity', 15);

        $this->postJson("/api/products/{$product->id}/stock-out", ['quantity' => 3])
            ->assertOk()
            ->assertJsonPath('product_quantity', 12);

        // Removing more than is on hand is rejected by the service.
        $this->postJson("/api/products/{$product->id}/stock-out", ['quantity' => 999])
            ->assertStatus(422);

        $this->postJson("/api/products/{$product->id}/set-quantity", ['quantity' => 42])
            ->assertOk()
            ->assertJsonPath('product_quantity', 42);
    }

    /** @test */
    public function it_searches_the_product_catalogue(): void
    {
        $this->actingAsApiUser();
        $product = $this->makeProduct(['product_name' => 'Findable Gadget']);

        $this->getJson('/api/products/search?term=Findable')
            ->assertOk()
            ->assertJsonFragment(['id' => $product->id]);
    }

    /** @test */
    public function it_filters_the_product_catalogue_by_stock_status(): void
    {
        $this->actingAsApiUser();

        $inStock = $this->makeProduct(['product_quantity' => 50, 'product_stock_alert' => 5]);
        $lowStock = $this->makeProduct(['product_quantity' => 3, 'product_stock_alert' => 5]);
        $outOfStock = $this->makeProduct(['product_quantity' => 0, 'product_stock_alert' => 5]);

        // Assert on the returned product id list: product and category ids can
        // collide, so a recursive assertJsonMissing(['id' => ...]) would match
        // the nested category object rather than the product row.
        $this->assertSame([$lowStock->id], $this->productIdsFor('low_stock'));
        $this->assertSame([$outOfStock->id], $this->productIdsFor('out_of_stock'));
        $this->assertSame([$inStock->id], $this->productIdsFor('in_stock'));

        $this->getJson('/api/products?stock_status=bogus')
            ->assertStatus(422);
    }

    /**
     * @return array<int, int> product ids returned by the stock-status filter
     */
    private function productIdsFor(string $status): array
    {
        return collect(
            $this->getJson('/api/products?stock_status='.$status)->assertOk()->json('data')
        )->pluck('id')->all();
    }

    /** @test */
    public function it_returns_the_stock_status_with_product_details(): void
    {
        $this->actingAsApiUser();
        $product = $this->makeProduct(['product_quantity' => 3, 'product_stock_alert' => 5]);

        $this->getJson("/api/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('stock_status', 'low_stock');
    }

    /** @test */
    public function it_exposes_the_low_stock_report(): void
    {
        $this->actingAsApiUser();
        $this->makeProduct(['product_quantity' => 0, 'product_stock_alert' => 5]);

        $this->getJson('/api/reports/low-stock/out-of-stock-count')
            ->assertOk()
            ->assertJsonPath('out_of_stock_count', 1);

        $this->getJson('/api/reports/low-stock')
            ->assertOk()
            ->assertJsonStructure(['data', 'total']);
    }
}
