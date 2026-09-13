<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'category_code' => 'CAT-BEV',
                'category_name' => 'Beverages',
                'description' => 'Soft drinks, juices, water and other beverages.',
                'color' => '#3498DB',
                'is_active' => true,
            ],
            [
                'category_code' => 'CAT-GRO',
                'category_name' => 'Groceries',
                'description' => 'Dry goods, canned food and household staples.',
                'color' => '#27AE60',
                'is_active' => true,
            ],
            [
                'category_code' => 'CAT-CLN',
                'category_name' => 'Cleaning',
                'description' => 'Detergents, soaps and cleaning supplies.',
                'color' => '#E67E22',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['category_code' => $category['category_code']],
                $category
            );
        }
    }
}
