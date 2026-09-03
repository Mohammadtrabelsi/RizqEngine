<?php

namespace Tests\Feature;

use App\Exceptions\StockInconsistencyException;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockExit;
use App\Services\StockExitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockExitPartialReturnTest extends TestCase
{
    use RefreshDatabase;

    protected function makeProduct(int $quantity): Product
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

    private function service(): StockExitService
    {
        return app(StockExitService::class);
    }

    /** @test */
    public function several_partial_returns_accumulate_and_close_only_when_settled()
    {
        $product = $this->makeProduct(100);
        $service = $this->service();

        $exit = $service->createExit(
            ['date' => now()->toDateString()],
            [['product_id' => $product->id, 'quantity' => 30]],
        );
        $detail = $exit->details()->first();

        // 100 out of stock, 30 left -> 70 on hand.
        $this->assertSame(70, $product->fresh()->product_quantity);
        $this->assertSame(30, $detail->fresh()->outstanding_quantity);

        // First partial return: 10 back.
        $service->createEntry($exit, [['detail_id' => $detail->id, 'returned' => 10]]);
        $exit->refresh();
        $detail->refresh();

        $this->assertSame(StockExit::STATUS_IN_TRANSIT, $exit->status);
        $this->assertSame(10, $detail->returned_quantity);
        $this->assertSame(20, $detail->outstanding_quantity);
        $this->assertSame(80, $product->fresh()->product_quantity);

        // Second return: 15 back + 5 written off as loss -> line fully settled.
        $service->createEntry($exit, [['detail_id' => $detail->id, 'returned' => 15, 'lost' => 5]]);
        $exit->refresh();
        $detail->refresh();

        $this->assertSame(StockExit::STATUS_CLOSED, $exit->status);
        $this->assertSame(25, $detail->returned_quantity);
        $this->assertSame(5, $detail->lost_quantity);
        $this->assertSame(0, $detail->outstanding_quantity);
        $this->assertSame(95, $product->fresh()->product_quantity);

        // Two Bons d'Entrée linked to the exit.
        $this->assertSame(2, $exit->entries()->count());
    }

    /** @test */
    public function a_return_larger_than_outstanding_is_refused()
    {
        $product = $this->makeProduct(100);
        $service = $this->service();

        $exit = $service->createExit(
            ['date' => now()->toDateString()],
            [['product_id' => $product->id, 'quantity' => 30]],
        );
        $detail = $exit->details()->first();

        $service->createEntry($exit, [['detail_id' => $detail->id, 'returned' => 25]]);

        $this->expectException(StockInconsistencyException::class);
        // Only 5 outstanding, cannot return 6.
        $service->createEntry($exit, [['detail_id' => $detail->id, 'returned' => 6]]);
    }

    /** @test */
    public function a_closed_exit_refuses_further_returns()
    {
        $product = $this->makeProduct(100);
        $service = $this->service();

        $exit = $service->createExit(
            ['date' => now()->toDateString()],
            [['product_id' => $product->id, 'quantity' => 20]],
        );
        $detail = $exit->details()->first();

        $service->createEntry($exit, [['detail_id' => $detail->id, 'returned' => 20]]);
        $this->assertSame(StockExit::STATUS_CLOSED, $exit->fresh()->status);

        $this->expectException(StockInconsistencyException::class);
        $service->createEntry($exit->fresh(), [['detail_id' => $detail->id, 'returned' => 1]]);
    }
}
