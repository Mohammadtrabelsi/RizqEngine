<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vehicles = [
            [
                'registration' => '123-TU-4567',
                'brand' => 'Renault',
                'model' => 'Master',
                'note' => 'Refrigerated delivery van.',
            ],
            [
                'registration' => '890-TU-1234',
                'brand' => 'Isuzu',
                'model' => 'NPR',
                'note' => 'Light truck for bulk deliveries.',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::firstOrCreate(
                ['registration' => $vehicle['registration']],
                $vehicle
            );
        }
    }
}
