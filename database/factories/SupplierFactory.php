<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Supplier>
     */
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'supplier_name' => $this->faker->name(),
            'supplier_email' => $this->faker->safeEmail(),
            'supplier_phone' => $this->faker->phoneNumber(),
            'tax_identification_number' => $this->faker->numerify('TIN-#########'),
            'city' => $this->faker->city(),
            'country' => $this->faker->country(),
            'address' => $this->faker->streetAddress(),
        ];
    }
}
