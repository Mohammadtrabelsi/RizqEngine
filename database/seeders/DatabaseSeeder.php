<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Only the super admin account, roles/permissions and base
        // configuration are seeded. A fresh install starts with an empty
        // catalogue, address book and no demo transactions; the business
        // loads its own data through the CSV import screens (example files
        // ship in storage/app/import-examples).
        $this->call(PermissionsTableSeeder::class);
        $this->call(SuperUserSeeder::class);
        $this->call(RoleUsersSeeder::class);
        $this->call(CurrencyDatabaseSeeder::class);
        $this->call(TaxDatabaseSeeder::class);
        $this->call(SettingDatabaseSeeder::class);
        $this->call(UnitDatabaseSeeder::class);
        $this->call(WarehouseDatabaseSeeder::class);
    }
}
