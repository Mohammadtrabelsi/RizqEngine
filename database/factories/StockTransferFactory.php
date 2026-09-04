<?php

namespace Database\Factories;

use App\Enums\TransferStatus;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockTransfer>
 */
class StockTransferFactory extends Factory
{
    protected $model = StockTransfer::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        return [
            'reference' => make_reference_id('TR', $sequence),
            'from_warehouse_id' => Warehouse::factory(),
            'to_warehouse_id' => Warehouse::factory(),
            'date' => now()->toDateString(),
            'status' => TransferStatus::Completed,
            'note' => $this->faker->optional()->sentence(),
        ];
    }
}
