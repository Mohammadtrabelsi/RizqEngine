<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VehicleDriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            [
                'name' => 'Ahmed Ben Ali',
                'email' => 'ahmed.driver@test.com',
                'phone' => '+216 20 100 101',
                'license_number' => 'TN-AH-1001',
            ],
            [
                'name' => 'Sami Trabelsi',
                'email' => 'sami.driver@test.com',
                'phone' => '+216 20 100 102',
                'license_number' => 'TN-ST-1002',
            ],
            [
                'name' => 'Nadia Mansour',
                'email' => 'nadia.driver@test.com',
                'phone' => '+216 20 100 103',
                'license_number' => 'TN-NM-1003',
            ],
        ];

        foreach ($drivers as $driverData) {
            $user = User::firstOrCreate(
                ['email' => $driverData['email']],
                [
                    'name' => $driverData['name'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true,
                ]
            );

            Driver::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->name,
                    'phone' => $driverData['phone'],
                    'license_number' => $driverData['license_number'],
                ]
            );
        }

        Vehicle::firstOrCreate(
            ['registration' => '201-TUN-1001'],
            ['brand' => 'Toyota', 'model' => 'Hilux']
        );
        Vehicle::firstOrCreate(
            ['registration' => '202-TUN-1002'],
            ['brand' => 'Renault', 'model' => 'Master']
        );
        Vehicle::firstOrCreate(
            ['registration' => '203-TUN-1003'],
            ['brand' => 'Iveco', 'model' => 'Daily']
        );
    }
}
