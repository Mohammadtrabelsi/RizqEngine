<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Converts the human-readable columns of the catalogue models into JSON-backed
 * translatable columns for spatie/laravel-translatable.
 *
 * Two things happen per column:
 *   1. VARCHAR columns are widened to TEXT so the locale-keyed JSON payload
 *      ({"en":..,"ar":..,"fr":..}) always fits.
 *   2. Any legacy plain-text value is wrapped into a JSON object under the
 *      application's default locale, because HasTranslations reads the column
 *      as JSON and would otherwise treat a bare string as an empty translation.
 */
return new class extends Migration
{
    /**
     * table => [
     *   'widen' => VARCHAR columns to promote to TEXT, keyed by column with the
     *              column's original nullability so the constraint is preserved,
     *   'text'  => columns already stored as TEXT that only need their legacy
     *              values wrapping into JSON.
     * ]
     *
     * @var array<string, array{widen: array<string, bool>, text: array<int, string>}>
     */
    private array $map = [
        'categories' => ['widen' => ['category_name' => false], 'text' => []],
        'products' => ['widen' => ['product_name' => false], 'text' => ['product_note']],
        'expense_categories' => ['widen' => ['category_name' => false], 'text' => ['category_description']],
        'currencies' => ['widen' => ['currency_name' => false], 'text' => []],
        'units' => ['widen' => ['name' => true, 'short_name' => true], 'text' => []],
    ];

    public function up(): void
    {
        $locale = config('app.fallback_locale', config('app.locale', 'en'));

        foreach ($this->map as $table => $groups) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $this->widen($table, $groups['widen']);

            foreach ($this->columnsOf($groups) as $column) {
                $this->wrapExisting($table, $column, $locale);
            }
        }
    }

    public function down(): void
    {
        $locale = config('app.fallback_locale', config('app.locale', 'en'));

        foreach ($this->map as $table => $groups) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($this->columnsOf($groups) as $column) {
                $this->unwrap($table, $column, $locale);
            }

            // Restore the widened columns back to VARCHAR on drivers that
            // distinguish the two, keeping their original nullability. TEXT
            // columns are left as they were.
            if ($this->supportsColumnChange()) {
                Schema::table($table, function (Blueprint $blueprint) use ($groups) {
                    foreach ($groups['widen'] as $column => $nullable) {
                        $definition = $blueprint->string($column);
                        $definition->nullable($nullable)->change();
                    }
                });
            }
        }
    }

    /**
     * @param  array{widen: array<string, bool>, text: array<int, string>}  $groups
     * @return array<int, string>
     */
    private function columnsOf(array $groups): array
    {
        return array_merge(array_keys($groups['widen']), $groups['text']);
    }

    /**
     * @param  array<string, bool>  $columns  column => nullable
     */
    private function widen(string $table, array $columns): void
    {
        if (empty($columns) || ! $this->supportsColumnChange()) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns) {
            foreach ($columns as $column => $nullable) {
                $definition = $blueprint->text($column);
                $definition->nullable($nullable)->change();
            }
        });
    }

    private function wrapExisting(string $table, string $column, string $locale): void
    {
        DB::table($table)->select('id', $column)->orderBy('id')->chunk(200, function ($rows) use ($table, $column, $locale) {
            foreach ($rows as $row) {
                $value = $row->{$column};

                if ($value === null || $this->looksLikeJson($value)) {
                    continue;
                }

                DB::table($table)->where('id', $row->id)->update([
                    $column => json_encode([$locale => $value], JSON_UNESCAPED_UNICODE),
                ]);
            }
        });
    }

    private function unwrap(string $table, string $column, string $locale): void
    {
        DB::table($table)->select('id', $column)->orderBy('id')->chunk(200, function ($rows) use ($table, $column, $locale) {
            foreach ($rows as $row) {
                $value = $row->{$column};

                if ($value === null || ! $this->looksLikeJson($value)) {
                    continue;
                }

                $decoded = json_decode($value, true);

                if (! is_array($decoded)) {
                    continue;
                }

                DB::table($table)->where('id', $row->id)->update([
                    $column => $decoded[$locale] ?? reset($decoded) ?: null,
                ]);
            }
        });
    }

    private function looksLikeJson(mixed $value): bool
    {
        return is_string($value) && Str::startsWith(ltrim($value), ['{', '[']);
    }

    private function supportsColumnChange(): bool
    {
        return DB::connection()->getDriverName() !== 'sqlite';
    }
};
