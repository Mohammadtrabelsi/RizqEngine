<?php

namespace Tests\Concerns;

use App\Models\Currency;
use App\Models\Setting;

/**
 * Creates the singleton settings record (with a default currency) that
 * views using format_currency() / settings() rely on.
 */
trait SeedsStoreSettings
{
    protected function seedStoreSettings(array $overrides = []): Setting
    {
        $currency = Currency::create([
            'currency_name' => 'US Dollar',
            'code' => 'USD',
            'symbol' => '$',
            'thousand_separator' => ',',
            'decimal_separator' => '.',
            'exchange_rate' => 1,
        ]);

        $settings = Setting::create(array_merge([
            'company_name' => 'Test Co',
            'company_email' => 'test@example.com',
            'company_phone' => '000',
            'default_currency_id' => $currency->id,
            'default_currency_position' => 'prefix',
            'notification_email' => 'test@example.com',
            'footer_text' => 'footer',
            'company_address' => 'address',
        ], $overrides));

        cache()->forget('settings');

        return $settings;
    }
}
