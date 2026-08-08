<?php

use Illuminate\Support\Facades\Storage;
use App\Models\Setting;

if (! function_exists('settings')) {
    function settings()
    {
        $settings = cache()->remember('settings', 24 * 60, function () {
            return Setting::firstOrFail();
        });

        return $settings;
    }
}

if (! function_exists('default_product_image')) {
    function default_product_image()
    {
        try {
            $path = settings()->default_product_image;
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
            $path = null;
        }

        return $path ? Storage::disk('public')->url($path) : asset('images/fallback_product_image.png');
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
