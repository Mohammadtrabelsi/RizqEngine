<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Seeder;

class TaxDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $taxes = [
            ['name' => 'TVA', 'type' => 'percentage', 'rate' => 19.00, 'apply_to' => 'product', 'order' => 0],
            ['name' => 'TVA 7', 'type' => 'percentage', 'rate' => 7.00, 'apply_to' => 'product', 'order' => 1],
            ['name' => 'Fodek', 'type' => 'percentage', 'rate' => 1.00, 'apply_to' => 'product', 'order' => 3],
            ['name' => 'Timbre', 'type' => 'fixed', 'rate' => 1.00, 'apply_to' => 'order', 'order' => 0],
            ['name' => 'TVA 0', 'type' => 'percentage', 'rate' => 0.00, 'apply_to' => 'product', 'order' => 0],
            ['name' => 'TVA 13', 'type' => 'percentage', 'rate' => 13.00, 'apply_to' => 'product', 'order' => 0],
        ];

        foreach ($taxes as $tax) {
            Tax::updateOrCreate(['name' => $tax['name']], $tax);
        }
    }
}
