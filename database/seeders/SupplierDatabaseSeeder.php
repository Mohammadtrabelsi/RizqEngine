<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suppliers = [
            [
                'supplier_name' => 'Atlas Distribution',
                'supplier_email' => 'contact@atlas-distribution.example',
                'supplier_phone' => '+216 71 000 001',
                'whatsapp_number' => '+216 71 000 001',
                'responsible_person' => 'Karim Ben Salah',
                'tax_identification_number' => 'TIN-100000001',
                'iban' => 'TN5910006035183598478831',
                'city' => 'Tunis',
                'country' => 'Tunisia',
                'address' => '12 Avenue Habib Bourguiba',
                'note' => 'Primary beverages and groceries supplier.',
            ],
            [
                'supplier_name' => 'Sahara Foods',
                'supplier_email' => 'sales@sahara-foods.example',
                'supplier_phone' => '+216 74 000 002',
                'whatsapp_number' => '+216 74 000 002',
                'responsible_person' => 'Amira Trabelsi',
                'tax_identification_number' => 'TIN-100000002',
                'iban' => 'TN5910006035183598478832',
                'city' => 'Sfax',
                'country' => 'Tunisia',
                'address' => '5 Rue de la République',
                'note' => 'Dry goods and canned food wholesaler.',
            ],
            [
                'supplier_name' => 'CleanPro Supplies',
                'supplier_email' => 'info@cleanpro.example',
                'supplier_phone' => '+216 73 000 003',
                'whatsapp_number' => '+216 73 000 003',
                'responsible_person' => 'Youssef Gharbi',
                'tax_identification_number' => 'TIN-100000003',
                'iban' => 'TN5910006035183598478833',
                'city' => 'Sousse',
                'country' => 'Tunisia',
                'address' => '20 Boulevard du 7 Novembre',
                'note' => 'Cleaning and hygiene products supplier.',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(
                ['supplier_email' => $supplier['supplier_email']],
                $supplier
            );
        }
    }
}
