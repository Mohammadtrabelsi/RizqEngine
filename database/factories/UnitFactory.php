<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Unit>
     */
    protected $model = Unit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
            'short_name' => $this->faker->unique()->lexify('??'),
            'operator' => $this->faker->randomElement(['*', '/']),
            'operation_value' => $this->faker->numberBetween(1, 100),
        ];
    }
}
