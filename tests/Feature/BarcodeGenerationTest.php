<?php

namespace Tests\Feature;

use App\Livewire\Barcode\ProductTable;
use App\Models\Product;
use Database\Seeders\ProductDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BarcodeGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_barcodes_are_generated_for_a_numeric_product_code(): void
    {
        $product = Product::factory()->create(['product_code' => '12345678']);

        Livewire::test(ProductTable::class)
            ->call('generateBarcodes', $product, 3)
            ->assertCount('barcodes', 3);
    }

    public function test_a_non_numeric_product_code_is_rejected_with_an_error(): void
    {
        $product = Product::factory()->create(['product_code' => 'ABC-0001']);

        Livewire::test(ProductTable::class)
            ->call('generateBarcodes', $product, 3)
            ->assertCount('barcodes', 0)
            ->assertSessionHas('error', trans('product.invalid-product-code'));
    }

    public function test_seeded_product_codes_are_always_numeric(): void
    {
        $this->seed(ProductDatabaseSeeder::class);

        $this->assertGreaterThan(0, Product::count());

        Product::query()->each(function (Product $product): void {
            $this->assertTrue(
                is_numeric($product->product_code),
                "Seeded product code [{$product->product_code}] is not numeric."
            );
        });
    }
}
