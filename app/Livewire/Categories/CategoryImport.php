<?php

namespace App\Livewire\Categories;

use App\Livewire\Imports\CsvImport;
use App\Models\Category;

/**
 * Bulk-import product categories from a CSV file.
 *
 * `category_code` must be unique (both against existing categories and within
 * the uploaded file); every other column maps directly to a category attribute.
 */
class CategoryImport extends CsvImport
{
    /** Category codes already taken, plus those seen earlier in the file. */
    private array $usedCodes = [];

    protected function gate(): string
    {
        return 'access_product_categories';
    }

    public function expectedColumns(): array
    {
        return [
            'category_code',
            'category_name',
            'description',
            'color',
            'is_active',
        ];
    }

    protected function requiredHeaders(): array
    {
        return ['category_code', 'category_name'];
    }

    protected function prepareLookups(): void
    {
        $this->usedCodes = Category::pluck('category_code')
            ->map(fn ($code) => strtolower((string) $code))
            ->all();
    }

    protected function mapRow(array $raw, array &$errors): array
    {
        $attributes = [
            'category_code' => $raw['category_code'] ?? null,
            'category_name' => $raw['category_name'] ?? null,
            'description' => $raw['description'] ?? null,
            'color' => $raw['color'] ?? null,
            'is_active' => $this->toBool($raw['is_active'] ?? null, true),
        ];

        // Duplicate category_code detection (existing categories and within the file).
        $code = strtolower((string) ($attributes['category_code'] ?? ''));
        if ($code !== '') {
            if (in_array($code, $this->usedCodes, true)) {
                $errors[] = (string) __('product.import_category_code_exists', ['code' => $attributes['category_code']]);
            }
            $this->usedCodes[] = $code;
        }

        return $attributes;
    }

    protected function rowRules(): array
    {
        return [
            'category_code' => ['required', 'string', 'max:255'],
            'category_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    protected function createRecord(array $attributes): void
    {
        Category::create($attributes);
    }

    public function redirectRouteName(): string
    {
        return 'product-categories.index';
    }

    public function langPrefix(): string
    {
        return 'category';
    }

    public function previewColumns(): array
    {
        return [
            'category_code' => __('product.category_code'),
            'category_name' => __('product.category_name'),
        ];
    }

    public function title(): string
    {
        return __('import.categories');
    }

    public function exampleFile(): string
    {
        return 'categories.csv';
    }

    /** Parse a truthy/falsy CSV cell into a boolean, falling back to $default when blank. */
    private function toBool(?string $value, bool $default = true): bool
    {
        $value = strtolower(trim((string) $value));

        if ($value === '') {
            return $default;
        }

        return in_array($value, ['1', 'true', 'yes', 'y', 'on'], true);
    }
}
