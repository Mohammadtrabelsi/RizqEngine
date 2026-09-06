<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\StockService;
use App\Services\WarehouseStockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseStockAllocationTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(int $quantity): Product
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

    public function test_stock_in_credits_the_given_warehouse(): void
    {
        $product = $this->makeProduct(0);
        $warehouse = Warehouse::factory()->create();
        $warehouseStock = app(WarehouseStockService::class);

        app(StockService::class)->stockIn($product, 40, null, 'Purchase', 1, $warehouse->id);

        $this->assertSame(40, $warehouseStock->quantityFor($product->fresh(), $warehouse));
        $this->assertSame(40, (int) $product->fresh()->product_quantity);
    }

    public function test_stock_in_without_warehouse_credits_the_default_warehouse(): void
    {
        $product = $this->makeProduct(0);
        $default = Warehouse::factory()->create(['is_default' => true]);
        $warehouseStock = app(WarehouseStockService::class);

        app(StockService::class)->stockIn($product, 25);

        $this->assertSame(25, $warehouseStock->quantityFor($product->fresh(), $default));
    }

    public function test_stock_out_draws_from_default_warehouse_first_then_overflows(): void
    {
        $product = $this->makeProduct(100);
        $default = Warehouse::factory()->create(['is_default' => true]);
        $other = Warehouse::factory()->create(['is_default' => false]);
        $warehouseStock = app(WarehouseStockService::class);

        $warehouseStock->increment($product, $default, 30);
        $warehouseStock->increment($product, $other, 50);

        // Sell 40: 30 from default (emptied), 10 from the overflow warehouse.
        app(StockService::class)->stockOut($product, 40, null, 'Sale', 1);

        $this->assertSame(0, $warehouseStock->quantityFor($product->fresh(), $default));
        $this->assertSame(40, $warehouseStock->quantityFor($product->fresh(), $other));
        $this->assertSame(60, (int) $product->fresh()->product_quantity);
    }

    public function test_stock_out_is_best_effort_when_pivot_is_short(): void
    {
        // Legacy stock: global quantity exists but no warehouse pivot rows.
        $product = $this->makeProduct(100);
        Warehouse::factory()->create(['is_default' => true]);

        // Must not throw even though warehouses hold nothing.
        app(StockService::class)->stockOut($product, 40, null, 'Sale', 1);

        $this->assertSame(60, (int) $product->fresh()->product_quantity);
    }
}
