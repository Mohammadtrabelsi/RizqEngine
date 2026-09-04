<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Batch>
 */
class BatchFactory extends Factory
{
    protected $model = Batch::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'warehouse_id' => null,
            'batch_number' => strtoupper($this->faker->unique()->bothify('LOT-#####')),
            'quantity' => $this->faker->numberBetween(1, 200),
            'manufactured_date' => now()->subMonths($this->faker->numberBetween(1, 6))->toDateString(),
            'expiry_date' => now()->addMonths($this->faker->numberBetween(1, 24))->toDateString(),
            'note' => $this->faker->optional()->sentence(),
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => ['expiry_date' => now()->subDays(5)->toDateString()]);
    }

    public function expiringWithin(int $days): static
    {
        return $this->state(fn () => ['expiry_date' => now()->addDays($days)->toDateString()]);
    }
}
