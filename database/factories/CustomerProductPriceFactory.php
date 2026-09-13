<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerProductPrice;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomerProductPrice>
 */
class CustomerProductPriceFactory extends Factory
{
    /** @var class-string<CustomerProductPrice> */
    protected $model = CustomerProductPrice::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'product_id' => Product::factory(),
            'price' => $this->faker->numberBetween(1, 500),
        ];
    }
}
