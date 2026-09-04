<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class PeopleDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        foreach ($this->suppliers() as $supplier) {
            Supplier::firstOrCreate(
                ['supplier_name' => $supplier['supplier_name']],
                $supplier
            );
        }

        foreach ($this->customers() as $customer) {
            Customer::firstOrCreate(
                ['customer_name' => $customer['customer_name']],
                $customer
            );
        }

        Model::reguard();
    }

    /**
     * Realistic wholesale suppliers the business purchases stock from.
     *
     * @return array<int, array<string, string>>
     */
    private function suppliers(): array
    {
        return [
            [
                'supplier_name' => 'SFBT Distribution',
                'supplier_email' => 'commandes@sfbt.com.tn',
                'supplier_phone' => '+216 71 336 522',
                'whatsapp_number' => '+216 98 336 522',
                'responsible_person' => 'Karim Ben Salah',
                'tax_identification_number' => '0004512M/A/M/000',
                'iban' => 'TN5904018104003005712589',
                'city' => 'Tunis',
                'country' => 'Tunisia',
                'address' => 'Rue de la Monnaie, 1000 Tunis',
                'note' => 'Beverages and mineral water distributor. Delivers on Mondays and Thursdays.',
            ],
            [
                'supplier_name' => 'Délice Danone',
                'supplier_email' => 'pro@delice-danone.tn',
                'supplier_phone' => '+216 73 210 400',
                'whatsapp_number' => '+216 52 210 400',
                'responsible_person' => 'Amel Trabelsi',
                'tax_identification_number' => '0018820P/A/M/000',
                'iban' => 'TN5910006035001002145874',
                'city' => 'Sousse',
                'country' => 'Tunisia',
                'address' => 'Zone Industrielle Sidi Abdelhamid, 4061 Sousse',
                'note' => 'Fresh dairy, yoghurt and milk supplier. Cold-chain delivery.',
            ],
            [
                'supplier_name' => 'Vitalait',
                'supplier_email' => 'ventes@vitalait.com.tn',
                'supplier_phone' => '+216 78 220 100',
                'whatsapp_number' => '+216 21 220 100',
                'responsible_person' => 'Sami Gharbi',
                'tax_identification_number' => '0025610R/A/M/000',
                'iban' => 'TN5914207207001007458963',
                'city' => 'Mahdia',
                'country' => 'Tunisia',
                'address' => 'Route de Sfax Km 3, 5100 Mahdia',
                'note' => 'UHT milk, butter and cream. Monthly volume agreement.',
            ],
            [
                'supplier_name' => 'Moulins de Carthage',
                'supplier_email' => 'contact@moulins-carthage.tn',
                'supplier_phone' => '+216 71 450 300',
                'whatsapp_number' => '+216 99 450 300',
                'responsible_person' => 'Nadia Jelassi',
                'tax_identification_number' => '0031277B/A/M/000',
                'iban' => 'TN5903509012000045781236',
                'city' => 'Ben Arous',
                'country' => 'Tunisia',
                'address' => 'Zone Industrielle, 2013 Ben Arous',
                'note' => 'Flour, semolina, pasta and couscous. Bulk pricing on pallets.',
            ],
            [
                'supplier_name' => 'Medina Fresh Produce',
                'supplier_email' => 'achat@medinafresh.tn',
                'supplier_phone' => '+216 74 610 250',
                'whatsapp_number' => '+216 55 610 250',
                'responsible_person' => 'Hichem Bouazizi',
                'tax_identification_number' => '0040918C/A/M/000',
                'iban' => 'TN5908006018002001478523',
                'city' => 'Sfax',
                'country' => 'Tunisia',
                'address' => 'Marché de Gros, 3000 Sfax',
                'note' => 'Fruits and vegetables. Daily fresh delivery, seasonal pricing.',
            ],
            [
                'supplier_name' => 'Nour Household & Care',
                'supplier_email' => 'orders@nourhousehold.tn',
                'supplier_phone' => '+216 71 890 745',
                'whatsapp_number' => '+216 27 890 745',
                'responsible_person' => 'Rania Mansour',
                'tax_identification_number' => '0052364D/A/M/000',
                'iban' => 'TN5901022015004005236987',
                'city' => 'Ariana',
                'country' => 'Tunisia',
                'address' => 'Rue du Lac Turkana, 2080 Ariana',
                'note' => 'Cleaning products, detergents and personal care. Net 30 terms.',
            ],
        ];
    }

    /**
     * Realistic customers the business sells to, mixing physical persons and
     * legal entities (which carry a matricule fiscal).
     *
     * @return array<int, array<string, string>>
     */
    private function customers(): array
    {
        return [
            [
                'customer_name' => 'Restaurant Dar El Jeld',
                'client_type' => Customer::TYPE_LEGAL_ENTITY,
                'customer_email' => 'contact@dareljeld.tn',
                'customer_phone' => '+216 71 560 916',
                'whatsapp_number' => '+216 98 560 916',
                'responsible_person' => 'Faycal Ben Ammar',
                'tax_identification_number' => '0061245E/A/M/000',
                'iban' => 'TN5904018104003009874521',
                'city' => 'Tunis',
                'country' => 'Tunisia',
                'address' => '5-10 Rue Dar El Jeld, 1006 Tunis',
                'note' => 'Fine-dining restaurant. Weekly standing order.',
            ],
            [
                'customer_name' => 'Hôtel Laico Tunis',
                'client_type' => Customer::TYPE_LEGAL_ENTITY,
                'customer_email' => 'purchasing@laico-tunis.com',
                'customer_phone' => '+216 71 951 000',
                'whatsapp_number' => '+216 20 951 000',
                'responsible_person' => 'Sonia Kallel',
                'tax_identification_number' => '0072310F/A/M/000',
                'iban' => 'TN5910006035001008745123',
                'city' => 'Tunis',
                'country' => 'Tunisia',
                'address' => 'Avenue Mohamed V, 1002 Tunis',
                'note' => 'Hotel purchasing department. Consolidated monthly invoicing.',
            ],
            [
                'customer_name' => 'Supérette El Baraka',
                'client_type' => Customer::TYPE_LEGAL_ENTITY,
                'customer_email' => 'gerance@superette-elbaraka.tn',
                'customer_phone' => '+216 73 445 210',
                'whatsapp_number' => '+216 22 445 210',
                'responsible_person' => 'Mounir Chaabane',
                'tax_identification_number' => '0083476G/A/M/000',
                'iban' => 'TN5914207207001003654789',
                'city' => 'Sousse',
                'country' => 'Tunisia',
                'address' => 'Rue de Palestine, 4000 Sousse',
                'note' => 'Neighbourhood mini-market. Buys in medium volumes.',
            ],
            [
                'customer_name' => 'Café Sidi Bou Said',
                'client_type' => Customer::TYPE_LEGAL_ENTITY,
                'customer_email' => 'gerant@cafesidibou.tn',
                'customer_phone' => '+216 71 740 631',
                'whatsapp_number' => '+216 24 740 631',
                'responsible_person' => 'Yassine Ferchichi',
                'tax_identification_number' => '0094512H/A/M/000',
                'iban' => 'TN5903509012000067894123',
                'city' => 'Sidi Bou Said',
                'country' => 'Tunisia',
                'address' => 'Rue Habib Thameur, 2026 Sidi Bou Said',
                'note' => 'Traditional café. Regular beverage and dairy orders.',
            ],
            [
                'customer_name' => 'Boulangerie La Gourmandise',
                'client_type' => Customer::TYPE_LEGAL_ENTITY,
                'customer_email' => 'contact@lagourmandise.tn',
                'customer_phone' => '+216 74 228 550',
                'whatsapp_number' => '+216 50 228 550',
                'responsible_person' => 'Ines Baccouche',
                'tax_identification_number' => '0105837J/A/M/000',
                'iban' => 'TN5908006018002009632587',
                'city' => 'Sfax',
                'country' => 'Tunisia',
                'address' => 'Avenue Habib Bourguiba, 3000 Sfax',
                'note' => 'Bakery buying flour and dairy in bulk.',
            ],
            [
                'customer_name' => 'Mohamed Ali Khelifi',
                'client_type' => Customer::TYPE_PHYSICAL_PERSON,
                'customer_email' => 'ma.khelifi@gmail.com',
                'customer_phone' => '+216 98 214 763',
                'whatsapp_number' => '+216 98 214 763',
                'responsible_person' => null,
                'tax_identification_number' => null,
                'iban' => null,
                'city' => 'Ariana',
                'country' => 'Tunisia',
                'address' => 'Cité Ennasr 2, 2037 Ariana',
                'note' => 'Regular walk-in retail customer.',
            ],
            [
                'customer_name' => 'Leila Ben Youssef',
                'client_type' => Customer::TYPE_PHYSICAL_PERSON,
                'customer_email' => 'leila.benyoussef@yahoo.fr',
                'customer_phone' => '+216 22 908 445',
                'whatsapp_number' => '+216 22 908 445',
                'responsible_person' => null,
                'tax_identification_number' => null,
                'iban' => null,
                'city' => 'La Marsa',
                'country' => 'Tunisia',
                'address' => 'Rue Taieb Mhiri, 2078 La Marsa',
                'note' => 'Loyal retail customer, prefers weekend pickups.',
            ],
            [
                'customer_name' => 'Anis Gharbi',
                'client_type' => Customer::TYPE_PHYSICAL_PERSON,
                'customer_email' => 'anis.gharbi@outlook.com',
                'customer_phone' => '+216 55 662 178',
                'whatsapp_number' => '+216 55 662 178',
                'responsible_person' => null,
                'tax_identification_number' => null,
                'iban' => null,
                'city' => 'Nabeul',
                'country' => 'Tunisia',
                'address' => 'Avenue Habib Thameur, 8000 Nabeul',
                'note' => 'Occasional retail customer.',
            ],
        ];
    }
}
