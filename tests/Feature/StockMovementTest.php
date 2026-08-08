<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Exceptions\InsufficientStockException;
use App\Services\StockService;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    protected function makeProduct(int $quantity = 0): Product
    {
        $category = Category::create([
            'category_code' => 'CAT',
            'category_name' => 'Category',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'product_name' => 'Widget',
            'product_code' => 'W-'.uniqid(),
            'product_quantity' => $quantity,
            'product_cost' => 10,
            'product_price' => 15,
            'product_stock_alert' => 5,
        ]);
    }

    /** @test */
    public function creating_a_product_with_stock_records_an_opening_movement()
    {
        $product = $this->makeProduct(20);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'opening',
            'quantity' => 20,
            'quantity_before' => 0,
            'quantity_after' => 20,
        ]);
    }

    /** @test */
    public function increasing_the_quantity_records_an_in_movement()
    {
        $product = $this->makeProduct(10);

        $product->update(['product_quantity' => 18]);

        $movement = StockMovement::where('product_id', $product->id)->where('type', 'in')->latest()->first();

        $this->assertNotNull($movement);
        $this->assertEquals(8, $movement->quantity);
        $this->assertEquals(10, $movement->quantity_before);
        $this->assertEquals(18, $movement->quantity_after);
    }

    /** @test */
    public function decreasing_the_quantity_records_an_out_movement()
    {
        $product = $this->makeProduct(10);

        $product->update(['product_quantity' => 4]);

        $movement = StockMovement::where('product_id', $product->id)->where('type', 'out')->latest()->first();

        $this->assertNotNull($movement);
        $this->assertEquals(6, $movement->quantity);
        $this->assertEquals(-6, $movement->signed_quantity);
    }

    /** @test */
    public function stock_service_stock_in_adds_stock_and_attributes_the_movement()
    {
        $product = $this->makeProduct(10);

        app(StockService::class)->stockIn($product, 5, 'Received shipment', 'Manual', 99);

        $this->assertEquals(15, $product->fresh()->product_quantity);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 5,
            'reference_type' => 'Manual',
            'reference_id' => 99,
            'note' => 'Received shipment',
        ]);
    }

    /** @test */
    public function stock_service_stock_out_throws_and_leaves_stock_untouched_when_insufficient()
    {
        $product = $this->makeProduct(3);

        try {
            app(StockService::class)->stockOut($product, 10);
            $this->fail('Expected InsufficientStockException was not thrown.');
        } catch (InsufficientStockException $e) {
            // expected
        }

        $this->assertEquals(3, $product->fresh()->product_quantity);
        $this->assertDatabaseMissing('stock_movements', [
            'product_id' => $product->id,
            'type' => 'out',
        ]);
    }

    /** @test */
    public function the_authenticated_user_is_recorded_as_the_causer_of_a_movement()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = $this->makeProduct(0);
        $product->update(['product_quantity' => 7]);

        $movement = StockMovement::where('product_id', $product->id)->where('type', 'in')->latest()->first();

        $this->assertEquals($user->id, $movement->user_id);
    }
}
