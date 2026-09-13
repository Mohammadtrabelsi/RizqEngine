<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = [
            [
                'customer_name' => 'Boutique El Medina',
                'client_type' => Customer::TYPE_LEGAL_ENTITY,
                'customer_email' => 'contact@elmedina.example',
                'customer_phone' => '+216 71 100 001',
                'whatsapp_number' => '+216 71 100 001',
                'responsible_person' => 'Salma Jaziri',
                'tax_identification_number' => 'TIN-200000001',
                'iban' => 'TN5910006035183598479001',
                'city' => 'Tunis',
                'country' => 'Tunisia',
                'address' => '3 Rue de Rome',
                'note' => 'Retail boutique, weekly orders.',
                'credit_limit' => 500000,
                'current_balance' => 0,
            ],
            [
                'customer_name' => 'Supérette Ennour',
                'client_type' => Customer::TYPE_LEGAL_ENTITY,
                'customer_email' => 'orders@ennour.example',
                'customer_phone' => '+216 74 100 002',
                'whatsapp_number' => '+216 74 100 002',
                'responsible_person' => 'Hatem Khelifi',
                'tax_identification_number' => 'TIN-200000002',
                'iban' => 'TN5910006035183598479002',
                'city' => 'Sfax',
                'country' => 'Tunisia',
                'address' => '18 Avenue Ali Belhouane',
                'note' => 'Neighbourhood grocery store.',
                'credit_limit' => 300000,
                'current_balance' => 0,
            ],
            [
                'customer_name' => 'Mohamed Aloui',
                'client_type' => Customer::TYPE_PHYSICAL_PERSON,
                'customer_email' => 'mohamed.aloui@example.com',
                'customer_phone' => '+216 22 100 003',
                'whatsapp_number' => '+216 22 100 003',
                'responsible_person' => null,
                'tax_identification_number' => null,
                'iban' => null,
                'city' => 'Sousse',
                'country' => 'Tunisia',
                'address' => '7 Rue Ibn Khaldoun',
                'note' => 'Individual walk-in customer.',
                'credit_limit' => 0,
                'current_balance' => 0,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                ['customer_email' => $customer['customer_email']],
                $customer
            );
        }
    }
}
