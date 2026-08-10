<?php

namespace App\Traits;

use Illuminate\Support\Arr;

/**
 * Complements Spatie's HasTranslations by guaranteeing that every configured
 * locale has a value.
 *
 * When a record is created (or a translatable attribute is edited) the user
 * only ever types the value for the locale they are currently using. Without
 * this trait the other languages would be stored blank and the UI would show
 * nothing once the visitor switches locale. Here we copy the value the user
 * did provide into every locale that is still empty, so the application always
 * has something sensible to display — the placeholder simply being the same
 * text until a real translation is entered later.
 *
 * Requires the model to also use Spatie\Translatable\HasTranslations.
 */
trait HasDefaultTranslations
{
    public static function bootHasDefaultTranslations(): void
    {
        static::saving(function ($model): void {
            $model->fillMissingTranslations();
        });
    }

    /**
     * Copy each translatable attribute's primary value into every locale that
     * does not have its own value yet.
     */
    public function fillMissingTranslations(): void
    {
        $locales = config('app.available_locales', [config('app.locale')]);

        foreach ($this->getTranslatableAttributes() as $attribute) {
            $translations = $this->getTranslations($attribute);

            $source = $this->resolveDefaultTranslation($translations);

            if ($source === null || $source === '') {
                continue;
            }

            foreach ($locales as $locale) {
                if (empty($translations[$locale] ?? null)) {
                    $this->setTranslation($attribute, $locale, $source);
                }
            }
        }
    }

    /**
     * Pick the best value to seed the missing locales with: prefer the current
     * locale, then the fallback locale, then whatever was provided first.
     *
     * @param  array<string, string>  $translations
     */
    protected function resolveDefaultTranslation(array $translations): ?string
    {
        return $translations[app()->getLocale()]
            ?? $translations[config('app.fallback_locale')]
            ?? Arr::first($translations);
    }
}
