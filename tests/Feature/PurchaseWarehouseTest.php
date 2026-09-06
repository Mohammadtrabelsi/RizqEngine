<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\PurchaseService;
use App\Services\WarehouseStockService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseWarehouseTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Cart::instance('purchase')->destroy();

        parent::tearDown();
    }

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

    private function addCartItem(Product $product, int $qty, float $price): void
    {
        Cart::instance('purchase')->add([
            'id' => $product->id,
            'name' => 'Widget',
            'qty' => $qty,
            'price' => $price,
            'weight' => 1,
            'options' => [
                'product_discount' => 0.00,
                'product_discount_type' => 'fixed',
                'sub_total' => $price * $qty,
                'code' => $product->product_code,
                'stock' => $product->product_quantity,
                'unit' => 'PC',
                'product_tax' => 0.00,
                'unit_price' => $price,
            ],
        ]);
    }

    public function test_completed_purchase_stocks_the_chosen_warehouse(): void
    {
        $product = $this->makeProduct(20);
        $supplier = Supplier::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $warehouseStock = app(WarehouseStockService::class);

        $this->addCartItem($product, 15, 10);

        $purchase = app(PurchaseService::class)->createPurchase([
            'date' => '2026-01-01',
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'tax_percentage' => 0,
            'discount_percentage' => 0,
            'shipping_amount' => 0,
            'paid_amount' => 0,
            'total_amount' => 150,
            'status' => 'Completed',
            'payment_method' => 'Cash',
            'note' => null,
        ]);

        $this->assertSame($warehouse->id, $purchase->warehouse_id);
        $this->assertSame(15, $warehouseStock->quantityFor($product->fresh(), $warehouse));
        $this->assertSame(35, (int) $product->fresh()->product_quantity);
    }
}
