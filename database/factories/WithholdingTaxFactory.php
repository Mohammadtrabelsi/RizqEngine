<?php

namespace Database\Factories;

use App\Enums\WithholdingCalculationBase;
use App\Models\WithholdingTax;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WithholdingTax>
 */
class WithholdingTaxFactory extends Factory
{
    protected $model = WithholdingTax::class;

    public function definition(): array
    {
        return [
            'name' => 'RAS '.$this->faker->unique()->numberBetween(1, 9999),
            'code' => 'RAS-'.$this->faker->unique()->numberBetween(1, 9999),
            'description' => $this->faker->sentence(),
            'rate' => $this->faker->randomElement([1, 1.5, 3, 5, 10, 15]),
            'calculation_base' => WithholdingCalculationBase::TTC->value,
            'active' => true,
            'applicable_to_purchases' => true,
            'applicable_to_sales' => false,
            'start_date' => null,
            'end_date' => null,
        ];
    }

    public function base(WithholdingCalculationBase $base): self
    {
        return $this->state(fn () => ['calculation_base' => $base->value]);
    }

    public function inactive(): self
    {
        return $this->state(fn () => ['active' => false]);
    }

    public function forSales(): self
    {
        return $this->state(fn () => ['applicable_to_sales' => true]);
    }
}
