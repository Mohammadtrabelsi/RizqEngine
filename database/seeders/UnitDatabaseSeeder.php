<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $units = [
            [
                'name' => ['en' => 'Piece', 'ar' => 'قطعة', 'fr' => 'Pièce'],
                'short_name' => ['en' => 'PC', 'ar' => 'PC', 'fr' => 'PC'],
                'operator' => '*',
                'operation_value' => 1,
            ],
            [
                'name' => ['en' => 'Metre', 'ar' => 'متر', 'fr' => 'Mètre'],
                'short_name' => ['en' => 'M', 'ar' => 'M', 'fr' => 'M'],
                'operator' => '*',
                'operation_value' => 1,
            ],
            [
                'name' => ['en' => 'Kilogramme', 'ar' => 'كيلوغرام', 'fr' => 'Kilogramme'],
                'short_name' => ['en' => 'KG', 'ar' => 'KG', 'fr' => 'KG'],
                'operator' => '*',
                'operation_value' => 1,
            ],
            [
                'name' => ['en' => 'Litre', 'ar' => 'لتر', 'fr' => 'Litre'],
                'short_name' => ['en' => 'L', 'ar' => 'L', 'fr' => 'L'],
                'operator' => '*',
                'operation_value' => 1,
            ],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['short_name->en' => $unit['short_name']['en']],
                $unit
            );
        }
    }
}
