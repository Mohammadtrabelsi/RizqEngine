<?php

namespace Tests\Feature;

use App\Livewire\Barcode\ProductTable;
use App\Models\Product;
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
            ->call('productSelected', $product)
            ->set("items.{$product->id}.quantity", 3)
            ->call('generateBarcodes')
            ->assertCount('labels', 3);
    }

    public function test_barcodes_are_generated_for_many_products(): void
    {
        $first = Product::factory()->create(['product_code' => '12345678']);
        $second = Product::factory()->create(['product_code' => '87654321']);

        Livewire::test(ProductTable::class)
            ->call('productSelected', $first)
            ->call('productSelected', $second)
            ->set("items.{$first->id}.quantity", 2)
            ->set("items.{$second->id}.quantity", 3)
            ->call('generateBarcodes')
            ->assertCount('labels', 5);
    }

    public function test_a_non_numeric_product_code_is_rejected_with_an_error(): void
    {
        $product = Product::factory()->create(['product_code' => 'ABC-0001']);

        Livewire::test(ProductTable::class)
            ->call('productSelected', $product)
            ->call('generateBarcodes')
            ->assertCount('labels', 0)
            ->assertSessionHas('error');
    }

    public function test_label_configuration_defaults_can_be_toggled(): void
    {
        $product = Product::factory()->create(['product_code' => '12345678']);

        Livewire::test(ProductTable::class)
            ->assertSet('showName', true)
            ->assertSet('showPrice', true)
            ->assertSet('showCode', false)
            ->call('productSelected', $product)
            ->call('generateBarcodes')
            ->assertCount('labels', 1)
            ->set('showPrice', false)
            ->assertCount('labels', 0);
    }
}
