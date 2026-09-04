<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class ProductDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Unit::firstOrCreate(
            ['short_name' => 'PC'],
            [
                'name' => 'Piece',
                'operator' => '*',
                'operation_value' => 1,
            ]
        );

        $categories = [];

        foreach ($this->categories() as $category) {
            $categories[$category['category_code']] = Category::firstOrCreate(
                ['category_code' => $category['category_code']],
                $category
            );
        }

        $suppliers = Supplier::pluck('id', 'supplier_name');

        foreach ($this->products() as $product) {
            $category = $categories[$product['category_code']] ?? null;

            if ($category === null) {
                continue;
            }

            Product::firstOrCreate(
                ['product_code' => $product['product_code']],
                [
                    'category_id' => $category->id,
                    'supplier_id' => $suppliers[$product['supplier_name']] ?? null,
                    'product_name' => $product['product_name'],
                    'product_barcode_symbology' => 'C128',
                    'product_quantity' => $product['product_quantity'],
                    'product_cost' => $product['product_cost'],
                    'product_price' => $product['product_price'],
                    'product_unit' => 'PC',
                    'product_stock_alert' => $product['product_stock_alert'],
                    'product_order_tax' => $product['product_order_tax'],
                    'product_tax_type' => 1,
                    'product_note' => $product['product_note'],
                    'expiry_date' => $product['expiry_date'],
                ]
            );
        }

        Model::reguard();
    }

    /**
     * Realistic product categories for a grocery / retail business.
     *
     * @return array<int, array<string, mixed>>
     */
    private function categories(): array
    {
        return [
            [
                'category_code' => 'BEV',
                'category_name' => 'Beverages',
                'description' => 'Water, soft drinks, juices and hot drinks.',
                'color' => '#2563EB',
                'is_active' => true,
            ],
            [
                'category_code' => 'DAIRY',
                'category_name' => 'Dairy & Eggs',
                'description' => 'Milk, yoghurt, cheese, butter and eggs.',
                'color' => '#F59E0B',
                'is_active' => true,
            ],
            [
                'category_code' => 'BAKERY',
                'category_name' => 'Bakery & Grains',
                'description' => 'Flour, semolina, pasta, couscous and bread.',
                'color' => '#D97706',
                'is_active' => true,
            ],
            [
                'category_code' => 'PRODUCE',
                'category_name' => 'Fruits & Vegetables',
                'description' => 'Fresh seasonal produce.',
                'color' => '#16A34A',
                'is_active' => true,
            ],
            [
                'category_code' => 'PANTRY',
                'category_name' => 'Pantry & Groceries',
                'description' => 'Oil, canned goods, sugar, coffee and staples.',
                'color' => '#9333EA',
                'is_active' => true,
            ],
            [
                'category_code' => 'HOME',
                'category_name' => 'Cleaning & Personal Care',
                'description' => 'Detergents, cleaning supplies and toiletries.',
                'color' => '#0891B2',
                'is_active' => true,
            ],
        ];
    }

    /**
     * Realistic products, priced in Tunisian dinars, mapped to their category
     * and (where applicable) their usual supplier.
     *
     * @return array<int, array<string, mixed>>
     */
    private function products(): array
    {
        return [
            // Beverages
            ['category_code' => 'BEV', 'supplier_name' => 'SFBT Distribution', 'product_name' => 'Safia Mineral Water 1.5L', 'product_code' => 'BEV-0001', 'product_quantity' => 240, 'product_cost' => 0.550, 'product_price' => 0.800, 'product_stock_alert' => 48, 'product_order_tax' => 7, 'product_note' => 'Still mineral water, 1.5 litre bottle.', 'expiry_date' => null],
            ['category_code' => 'BEV', 'supplier_name' => 'SFBT Distribution', 'product_name' => 'Boga Cidre 1L', 'product_code' => 'BEV-0002', 'product_quantity' => 120, 'product_cost' => 1.100, 'product_price' => 1.600, 'product_stock_alert' => 24, 'product_order_tax' => 19, 'product_note' => 'Apple-flavoured soft drink, 1 litre.', 'expiry_date' => null],
            ['category_code' => 'BEV', 'supplier_name' => 'SFBT Distribution', 'product_name' => 'Coca-Cola 33cl Can', 'product_code' => 'BEV-0003', 'product_quantity' => 300, 'product_cost' => 0.900, 'product_price' => 1.300, 'product_stock_alert' => 60, 'product_order_tax' => 19, 'product_note' => 'Carbonated soft drink can.', 'expiry_date' => null],
            ['category_code' => 'BEV', 'supplier_name' => 'Moulins de Carthage', 'product_name' => 'Café Bondin Ground 250g', 'product_code' => 'BEV-0004', 'product_quantity' => 80, 'product_cost' => 4.200, 'product_price' => 5.500, 'product_stock_alert' => 15, 'product_order_tax' => 19, 'product_note' => 'Ground roasted coffee, 250g pack.', 'expiry_date' => null],

            // Dairy & Eggs
            ['category_code' => 'DAIRY', 'supplier_name' => 'Vitalait', 'product_name' => 'Vitalait UHT Milk 1L', 'product_code' => 'DAI-0001', 'product_quantity' => 180, 'product_cost' => 1.200, 'product_price' => 1.450, 'product_stock_alert' => 36, 'product_order_tax' => 7, 'product_note' => 'Semi-skimmed UHT milk, 1 litre.', 'expiry_date' => '2026-12-15'],
            ['category_code' => 'DAIRY', 'supplier_name' => 'Délice Danone', 'product_name' => 'Délice Yoghurt Natural x4', 'product_code' => 'DAI-0002', 'product_quantity' => 96, 'product_cost' => 1.400, 'product_price' => 1.900, 'product_stock_alert' => 24, 'product_order_tax' => 7, 'product_note' => 'Pack of 4 natural yoghurts.', 'expiry_date' => '2026-10-05'],
            ['category_code' => 'DAIRY', 'supplier_name' => 'Vitalait', 'product_name' => 'Vitalait Butter 200g', 'product_code' => 'DAI-0003', 'product_quantity' => 60, 'product_cost' => 2.600, 'product_price' => 3.400, 'product_stock_alert' => 12, 'product_order_tax' => 7, 'product_note' => 'Salted table butter, 200g.', 'expiry_date' => '2026-11-20'],
            ['category_code' => 'DAIRY', 'supplier_name' => 'Medina Fresh Produce', 'product_name' => 'Fresh Eggs Tray of 30', 'product_code' => 'DAI-0004', 'product_quantity' => 40, 'product_cost' => 7.500, 'product_price' => 9.500, 'product_stock_alert' => 8, 'product_order_tax' => 7, 'product_note' => 'Tray of 30 medium eggs.', 'expiry_date' => '2026-09-25'],

            // Bakery & Grains
            ['category_code' => 'BAKERY', 'supplier_name' => 'Moulins de Carthage', 'product_name' => 'Wheat Flour 1kg', 'product_code' => 'BAK-0001', 'product_quantity' => 150, 'product_cost' => 0.900, 'product_price' => 1.250, 'product_stock_alert' => 30, 'product_order_tax' => 7, 'product_note' => 'All-purpose wheat flour, 1kg.', 'expiry_date' => '2027-03-01'],
            ['category_code' => 'BAKERY', 'supplier_name' => 'Moulins de Carthage', 'product_name' => 'Fine Couscous 1kg', 'product_code' => 'BAK-0002', 'product_quantity' => 110, 'product_cost' => 1.300, 'product_price' => 1.800, 'product_stock_alert' => 24, 'product_order_tax' => 7, 'product_note' => 'Fine-grain couscous, 1kg pack.', 'expiry_date' => '2027-05-01'],
            ['category_code' => 'BAKERY', 'supplier_name' => 'Moulins de Carthage', 'product_name' => 'Spaghetti Pasta 500g', 'product_code' => 'BAK-0003', 'product_quantity' => 200, 'product_cost' => 0.750, 'product_price' => 1.100, 'product_stock_alert' => 40, 'product_order_tax' => 7, 'product_note' => 'Durum wheat spaghetti, 500g.', 'expiry_date' => '2027-06-01'],

            // Fruits & Vegetables
            ['category_code' => 'PRODUCE', 'supplier_name' => 'Medina Fresh Produce', 'product_name' => 'Tomatoes 1kg', 'product_code' => 'PRD-0001', 'product_quantity' => 70, 'product_cost' => 1.200, 'product_price' => 1.800, 'product_stock_alert' => 15, 'product_order_tax' => 0, 'product_note' => 'Fresh local tomatoes, sold per kilo.', 'expiry_date' => '2026-09-10'],
            ['category_code' => 'PRODUCE', 'supplier_name' => 'Medina Fresh Produce', 'product_name' => 'Potatoes 1kg', 'product_code' => 'PRD-0002', 'product_quantity' => 130, 'product_cost' => 0.850, 'product_price' => 1.300, 'product_stock_alert' => 25, 'product_order_tax' => 0, 'product_note' => 'Fresh potatoes, sold per kilo.', 'expiry_date' => '2026-09-30'],
            ['category_code' => 'PRODUCE', 'supplier_name' => 'Medina Fresh Produce', 'product_name' => 'Oranges 1kg', 'product_code' => 'PRD-0003', 'product_quantity' => 90, 'product_cost' => 1.000, 'product_price' => 1.600, 'product_stock_alert' => 18, 'product_order_tax' => 0, 'product_note' => 'Seasonal Maltese oranges, per kilo.', 'expiry_date' => '2026-09-12'],

            // Pantry & Groceries
            ['category_code' => 'PANTRY', 'supplier_name' => 'Moulins de Carthage', 'product_name' => 'Olive Oil 1L', 'product_code' => 'PAN-0001', 'product_quantity' => 65, 'product_cost' => 11.500, 'product_price' => 14.900, 'product_stock_alert' => 12, 'product_order_tax' => 7, 'product_note' => 'Extra virgin olive oil, 1 litre.', 'expiry_date' => '2027-08-01'],
            ['category_code' => 'PANTRY', 'supplier_name' => 'Moulins de Carthage', 'product_name' => 'White Sugar 1kg', 'product_code' => 'PAN-0002', 'product_quantity' => 160, 'product_cost' => 1.050, 'product_price' => 1.400, 'product_stock_alert' => 30, 'product_order_tax' => 7, 'product_note' => 'Granulated white sugar, 1kg.', 'expiry_date' => null],
            ['category_code' => 'PANTRY', 'supplier_name' => 'Medina Fresh Produce', 'product_name' => 'Harissa Paste 135g', 'product_code' => 'PAN-0003', 'product_quantity' => 100, 'product_cost' => 1.300, 'product_price' => 1.950, 'product_stock_alert' => 20, 'product_order_tax' => 7, 'product_note' => 'Traditional hot chilli paste, 135g tin.', 'expiry_date' => '2027-02-01'],
            ['category_code' => 'PANTRY', 'supplier_name' => 'Medina Fresh Produce', 'product_name' => 'Canned Tuna 160g', 'product_code' => 'PAN-0004', 'product_quantity' => 140, 'product_cost' => 2.100, 'product_price' => 2.900, 'product_stock_alert' => 28, 'product_order_tax' => 7, 'product_note' => 'Tuna in olive oil, 160g can.', 'expiry_date' => '2028-01-01'],

            // Cleaning & Personal Care
            ['category_code' => 'HOME', 'supplier_name' => 'Nour Household & Care', 'product_name' => 'Dish Soap 750ml', 'product_code' => 'HOM-0001', 'product_quantity' => 85, 'product_cost' => 1.800, 'product_price' => 2.600, 'product_stock_alert' => 18, 'product_order_tax' => 19, 'product_note' => 'Concentrated dishwashing liquid, 750ml.', 'expiry_date' => null],
            ['category_code' => 'HOME', 'supplier_name' => 'Nour Household & Care', 'product_name' => 'Laundry Detergent 3kg', 'product_code' => 'HOM-0002', 'product_quantity' => 45, 'product_cost' => 8.900, 'product_price' => 11.500, 'product_stock_alert' => 10, 'product_order_tax' => 19, 'product_note' => 'Machine laundry powder, 3kg box.', 'expiry_date' => null],
            ['category_code' => 'HOME', 'supplier_name' => 'Nour Household & Care', 'product_name' => 'Toilet Paper 8 Rolls', 'product_code' => 'HOM-0003', 'product_quantity' => 70, 'product_cost' => 3.200, 'product_price' => 4.500, 'product_stock_alert' => 15, 'product_order_tax' => 19, 'product_note' => 'Two-ply toilet paper, pack of 8.', 'expiry_date' => null],
            ['category_code' => 'HOME', 'supplier_name' => 'Nour Household & Care', 'product_name' => 'Shampoo 400ml', 'product_code' => 'HOM-0004', 'product_quantity' => 55, 'product_cost' => 3.600, 'product_price' => 5.200, 'product_stock_alert' => 12, 'product_order_tax' => 19, 'product_note' => 'Everyday care shampoo, 400ml.', 'expiry_date' => '2027-12-01'],
        ];
    }
}
