<?php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warehouse>
 */
class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company().' Depot',
            'code' => strtoupper($this->faker->unique()->bothify('WH-###')),
            'phone' => $this->faker->optional()->phoneNumber(),
            'city' => $this->faker->city(),
            'address' => $this->faker->optional()->address(),
            'is_default' => false,
            'is_active' => true,
            'note' => $this->faker->optional()->sentence(),
        ];
    }

    public function default(): static
    {
        return $this->state(fn () => ['is_default' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
