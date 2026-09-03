<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'warehouse_id' => Warehouse::factory(),
            'name' => 'Aisle '.strtoupper($this->faker->randomLetter()).$this->faker->numberBetween(1, 20),
            'code' => strtoupper($this->faker->unique()->bothify('LOC-###')),
            'is_active' => true,
            'note' => $this->faker->optional()->sentence(),
        ];
    }
}
