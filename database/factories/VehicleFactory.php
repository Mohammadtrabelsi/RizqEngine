<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'registration' => strtoupper($this->faker->bothify('###-???-##')),
            'brand' => $this->faker->randomElement(['Renault', 'Peugeot', 'Toyota', 'Isuzu', 'Iveco']),
            'model' => $this->faker->bothify('Model-##'),
            'note' => $this->faker->optional()->sentence(),
        ];
    }
}
