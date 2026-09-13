<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Seeder;

class DriverDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $drivers = [
            [
                'name' => 'Slim Bouazizi',
                'phone' => '+216 98 200 001',
                'license_number' => 'TN-DRV-000001',
                'note' => 'Full-time delivery driver.',
            ],
            [
                'name' => 'Nabil Ferchichi',
                'phone' => '+216 98 200 002',
                'license_number' => 'TN-DRV-000002',
                'note' => 'Handles regional deliveries.',
            ],
        ];

        foreach ($drivers as $driver) {
            Driver::firstOrCreate(
                ['license_number' => $driver['license_number']],
                $driver
            );
        }
    }
}
