<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Product>
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $cost = $this->faker->numberBetween(5, 500);

        return [
            'category_id' => Category::factory(),
            'product_name' => $this->faker->words(3, true),
            'product_code' => (string) $this->faker->unique()->numberBetween(10000000, 99999999),
            'product_barcode_symbology' => 'C128',
            'product_quantity' => $this->faker->numberBetween(0, 100),
            'product_cost' => $cost,
            'product_price' => $cost + $this->faker->numberBetween(1, 200),
            'product_unit' => 'PC',
            'product_stock_alert' => $this->faker->numberBetween(1, 10),
            'product_order_tax' => $this->faker->numberBetween(0, 20),
            'product_tax_type' => $this->faker->numberBetween(1, 2),
            'product_note' => $this->faker->sentence(),
        ];
    }
}
