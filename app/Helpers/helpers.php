<?php

use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

if (! function_exists('settings')) {
    function settings()
    {
        $settings = cache()->remember('settings', 24 * 60, function () {
            return Setting::first() ?? new Setting;
        });

        return $settings;
    }
}

if (! function_exists('default_product_image')) {
    function default_product_image()
    {
        try {
            $path = settings()->default_product_image;
        } catch (Throwable $e) {
            $path = null;
        }

        return $path ? Storage::disk('public')->url($path) : asset('images/fallback_product_image.png');
    }
}

if (! function_exists('default_category_image')) {
    function default_category_image()
    {
        try {
            $path = settings()->default_category_image;
        } catch (Throwable $e) {
            $path = null;
        }

        return $path ? Storage::disk('public')->url($path) : asset('images/fallback_product_image.png');
    }
}

if (! function_exists('default_supplier_image')) {
    function default_supplier_image()
    {
        try {
            $path = settings()->default_supplier_image;
        } catch (Throwable $e) {
            $path = null;
        }

        return $path ? Storage::disk('public')->url($path) : asset('images/fallback_profile_image.png');
    }
}

if (! function_exists('default_customer_image')) {
    function default_customer_image()
    {
        try {
            $path = settings()->default_customer_image;
        } catch (Throwable $e) {
            $path = null;
        }

        return $path ? Storage::disk('public')->url($path) : asset('images/fallback_profile_image.png');
    }
}

if (! function_exists('format_currency')) {
    function format_currency($value, $format = true)
    {
        if (! $format) {
            return $value;
        }

        $settings = settings();
        $position = $settings->default_currency_position;
        $symbol = $settings->currency->symbol;
        $decimal_separator = $settings->currency->decimal_separator;
        $thousand_separator = $settings->currency->thousand_separator;

        if ($position == 'prefix') {
            $formatted_value = $symbol.number_format((float) $value, 2, $decimal_separator, $thousand_separator);
        } else {
            $formatted_value = number_format((float) $value, 2, $decimal_separator, $thousand_separator).$symbol;
        }

        return $formatted_value;
    }
}

if (! function_exists('translatable_string')) {
    /**
     * Resolve a translatable attribute value to a plain string.
     *
     * Spatie's HasTranslations serialises translatable attributes to the full
     * array of locale translations (e.g. ['en' => '...', 'ar' => '...']) when a
     * model is cast to an array/JSON — which happens when products are passed
     * through Livewire event payloads. This helper collapses such an array back
     * to a single string for the current locale (falling back to the app
     * fallback locale, then the first available translation), while leaving
     * plain strings and other scalars untouched.
     *
     * @param  mixed  $value
     */
    function translatable_string($value): string
    {
        if (is_array($value)) {
            $resolved = $value[app()->getLocale()]
                ?? $value[config('app.fallback_locale')]
                ?? Arr::first($value);

            return (string) ($resolved ?? '');
        }

        return (string) ($value ?? '');
    }
}

if (! function_exists('make_reference_id')) {
    function make_reference_id($prefix, $number)
    {
        $padded_text = $prefix.'-'.str_pad((string) $number, 5, '0', STR_PAD_LEFT);

        return $padded_text;
    }
}

if (! function_exists('array_merge_numeric_values')) {
    function array_merge_numeric_values()
    {
        $arrays = func_get_args();
        $merged = [];
        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (! is_numeric($value)) {
                    continue;
                }
                if (! isset($merged[$key])) {
                    $merged[$key] = $value;
                } else {
                    $merged[$key] += $value;
                }
            }
        }

        return $merged;
    }
}
