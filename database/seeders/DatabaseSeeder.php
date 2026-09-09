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
        $this->call(VehicleDriverSeeder::class);
        $this->call(RoleUsersSeeder::class);
        $this->call(CurrencyDatabaseSeeder::class);
        $this->call(TaxDatabaseSeeder::class);
        $this->call(SettingDatabaseSeeder::class);
        $this->call(PeopleDatabaseSeeder::class);
        $this->call(UnitDatabaseSeeder::class);
        $this->call(WarehouseDatabaseSeeder::class);

        // Products, categories, suppliers and customers are intentionally NOT
        // seeded: a fresh install starts with an empty catalogue and address
        // book. The business loads its own data through the CSV import screens
        // (example files ship in storage/app/import-examples). The seeders
        // below run but no-op while there is no product/customer data to build
        // sales, purchases, orders and returns on top of.
        $this->call(ProductDatabaseSeeder::class);
        $this->call(SaleDatabaseSeeder::class);
        $this->call(PurchaseDatabaseSeeder::class);
        $this->call(SalesReturnDatabaseSeeder::class);
        $this->call(PurchasesReturnDatabaseSeeder::class);
        $this->call(CommandeDatabaseSeeder::class);
        $this->call(ExpenseDatabaseSeeder::class);
    }
}
