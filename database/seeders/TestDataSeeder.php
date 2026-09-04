<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeds a representative set of realistic records (categories, products,
 * customers and suppliers) that tests and manual QA can rely on.
 *
 * The concrete data lives in PeopleDatabaseSeeder (customers and suppliers)
 * and ProductDatabaseSeeder (categories and products); this seeder simply
 * delegates to them so there is a single source of truth. Both underlying
 * seeders are idempotent, so running this repeatedly will not create
 * duplicate records.
 *
 * Run it on its own with:
 *     php artisan db:seed --class=Database\\Seeders\\TestDataSeeder
 */
class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Suppliers must exist before products so they can be linked.
        $this->call(PeopleDatabaseSeeder::class);
        $this->call(ProductDatabaseSeeder::class);
    }
}
