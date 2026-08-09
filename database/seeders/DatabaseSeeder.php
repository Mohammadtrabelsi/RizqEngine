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
        $this->call(PermissionsTableSeeder::class);
        $this->call(SuperUserSeeder::class);
        $this->call(RoleUsersSeeder::class);
        $this->call(CurrencyDatabaseSeeder::class);
        $this->call(SettingDatabaseSeeder::class);
        $this->call(ProductDatabaseSeeder::class);
        $this->call(PeopleDatabaseSeeder::class);
        $this->call(SaleDatabaseSeeder::class);
        $this->call(ExpenseDatabaseSeeder::class);
        $this->call(TestDataSeeder::class);

    }
}
