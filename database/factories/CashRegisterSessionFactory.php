<?php

namespace Database\Factories;

use App\Enums\CashRegisterStatus;
use App\Models\CashRegisterSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashRegisterSession>
 */
class CashRegisterSessionFactory extends Factory
{
    protected $model = CashRegisterSession::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'warehouse_id' => null,
            'opening_float' => 100_00,
            'status' => CashRegisterStatus::Open,
            'opened_at' => now(),
            'note' => null,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => CashRegisterStatus::Closed,
            'closing_amount' => 150_00,
            'expected_amount' => 150_00,
            'difference' => 0,
            'closed_at' => now(),
        ]);
    }
}
