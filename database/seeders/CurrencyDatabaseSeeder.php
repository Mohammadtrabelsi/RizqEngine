<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CurrencyDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Currency::create([
            'currency_name' => 'Tunisian Dinar',
            'code' => Str::upper('TND'),
            'symbol' => 'DT',
            'thousand_separator' => ',',
            'decimal_separator' => '.',
            'exchange_rate' => null,
        ]);
    }
}
