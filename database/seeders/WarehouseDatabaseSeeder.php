<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $warehouses = [
            [
                'name' => 'Main Warehouse',
                'code' => 'WH-MAIN',
                'phone' => null,
                'city' => null,
                'address' => null,
                'is_default' => true,
                'is_active' => true,
                'note' => null,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::firstOrCreate(
                ['code' => $warehouse['code']],
                $warehouse
            );
        }
    }
}
