<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Suppliers & customers seeder.
 *
 * The application intentionally ships with NO demo suppliers or customers:
 * a fresh install starts empty and the business loads its own records via the
 * CSV import screens (Suppliers → Import and Customers → Import). Ready-to-fill
 * example files are available from the "Download example" link on those screens
 * (see storage/app/import-examples/suppliers.csv and customers.csv).
 *
 * This seeder is kept as a deliberate no-op so the seeding chain and the
 * `db:seed --class=PeopleDatabaseSeeder` command stay valid.
 */
class PeopleDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // No demo suppliers or customers are seeded. Import real data via the
        // Suppliers and Customers import screens instead.
    }
}
