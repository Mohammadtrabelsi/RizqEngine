<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

/**
 * Seeds a representative set of dummy records (categories, products,
 * customers and suppliers) that tests and manual QA can rely on.
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
        // A handful of categories, each with a few products.
        Category::factory()
            ->count(5)
            ->has(Product::factory()->count(4), 'products')
            ->create();

        // People to buy from and sell to.
        Customer::factory()->count(15)->create();
        Supplier::factory()->count(6)->create();
    }
}
