<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Depends on categories and suppliers seeded by
     * CategoryDatabaseSeeder and SupplierDatabaseSeeder.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::pluck('id', 'category_code');
        $suppliers = Supplier::pluck('id', 'supplier_email');

        $products = [
            [
                'product_code' => 'PRD-0001',
                'product_name' => 'Mineral Water 1.5L',
                'category_code' => 'CAT-BEV',
                'supplier_email' => 'contact@atlas-distribution.example',
                'product_cost' => 0.60,
                'product_price' => 1.00,
                'product_quantity' => 200,
            ],
            [
                'product_code' => 'PRD-0002',
                'product_name' => 'Orange Juice 1L',
                'category_code' => 'CAT-BEV',
                'supplier_email' => 'contact@atlas-distribution.example',
                'product_cost' => 1.20,
                'product_price' => 2.20,
                'product_quantity' => 120,
            ],
            [
                'product_code' => 'PRD-0003',
                'product_name' => 'Cola Can 33cl',
                'category_code' => 'CAT-BEV',
                'supplier_email' => 'contact@atlas-distribution.example',
                'product_cost' => 0.50,
                'product_price' => 0.90,
                'product_quantity' => 300,
            ],
            [
                'product_code' => 'PRD-0004',
                'product_name' => 'Rice 1kg',
                'category_code' => 'CAT-GRO',
                'supplier_email' => 'sales@sahara-foods.example',
                'product_cost' => 1.10,
                'product_price' => 1.80,
                'product_quantity' => 150,
            ],
            [
                'product_code' => 'PRD-0005',
                'product_name' => 'Pasta 500g',
                'category_code' => 'CAT-GRO',
                'supplier_email' => 'sales@sahara-foods.example',
                'product_cost' => 0.70,
                'product_price' => 1.30,
                'product_quantity' => 180,
            ],
            [
                'product_code' => 'PRD-0006',
                'product_name' => 'Canned Tomatoes 400g',
                'category_code' => 'CAT-GRO',
                'supplier_email' => 'sales@sahara-foods.example',
                'product_cost' => 0.80,
                'product_price' => 1.40,
                'product_quantity' => 160,
            ],
            [
                'product_code' => 'PRD-0007',
                'product_name' => 'Olive Oil 1L',
                'category_code' => 'CAT-GRO',
                'supplier_email' => 'sales@sahara-foods.example',
                'product_cost' => 6.00,
                'product_price' => 9.50,
                'product_quantity' => 90,
            ],
            [
                'product_code' => 'PRD-0008',
                'product_name' => 'Dish Soap 750ml',
                'category_code' => 'CAT-CLN',
                'supplier_email' => 'info@cleanpro.example',
                'product_cost' => 1.50,
                'product_price' => 2.60,
                'product_quantity' => 110,
            ],
            [
                'product_code' => 'PRD-0009',
                'product_name' => 'Laundry Detergent 3kg',
                'category_code' => 'CAT-CLN',
                'supplier_email' => 'info@cleanpro.example',
                'product_cost' => 5.50,
                'product_price' => 8.90,
                'product_quantity' => 70,
            ],
            [
                'product_code' => 'PRD-0010',
                'product_name' => 'Bleach 2L',
                'category_code' => 'CAT-CLN',
                'supplier_email' => 'info@cleanpro.example',
                'product_cost' => 1.00,
                'product_price' => 1.90,
                'product_quantity' => 130,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['product_code' => $product['product_code']],
                [
                    'category_id' => $categories[$product['category_code']] ?? null,
                    'supplier_id' => $suppliers[$product['supplier_email']] ?? null,
                    'product_name' => $product['product_name'],
                    'product_barcode_symbology' => 'C128',
                    'product_quantity' => $product['product_quantity'],
                    'product_cost' => $product['product_cost'],
                    'product_price' => $product['product_price'],
                    'product_unit' => 'PC',
                    'product_stock_alert' => 10,
                    'product_stock_alert_max' => null,
                    'product_order_tax' => 0,
                    'product_tax_type' => 1,
                    'product_note' => null,
                    'expiry_date' => null,
                ]
            );
        }
    }
}
