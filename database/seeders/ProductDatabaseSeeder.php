<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Product & category seeder.
 *
 * The application intentionally ships with NO demo products or categories:
 * a fresh install starts with an empty catalogue and the business loads its
 * own products via the CSV import screen (Products → Import). A ready-to-fill
 * example file is available from the "Download example" link on that screen
 * (see storage/app/import-examples/products.csv).
 *
 * This seeder is kept as a deliberate no-op so the seeding chain and the
 * `db:seed --class=ProductDatabaseSeeder` command stay valid.
 */
class ProductDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // No demo products or categories are seeded. Import real data via
        // the Products import screen instead.
    }
}
