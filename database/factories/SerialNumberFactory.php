<?php

namespace Database\Factories;

use App\Enums\SerialStatus;
use App\Models\Product;
use App\Models\SerialNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SerialNumber>
 */
class SerialNumberFactory extends Factory
{
    protected $model = SerialNumber::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'batch_id' => null,
            'warehouse_id' => null,
            'serial' => strtoupper($this->faker->unique()->bothify('SN-########')),
            'status' => SerialStatus::InStock,
            'note' => $this->faker->optional()->sentence(),
        ];
    }

    public function sold(): static
    {
        return $this->state(fn () => ['status' => SerialStatus::Sold]);
    }
}
