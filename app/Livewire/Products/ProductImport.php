<?php

namespace App\Livewire\Products;

use App\Livewire\Imports\CsvImport;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Collection;

/**
 * Bulk-import products from a CSV file.
 *
 * The `category` and `supplier` columns accept either a numeric id or the
 * record's name; every other column maps directly to a product attribute.
 */
class ProductImport extends CsvImport
{
    private Collection $categoriesById;

    private Collection $categoriesByName;

    private Collection $suppliersById;

    private Collection $suppliersByName;

    /** Product codes already taken, plus those seen earlier in the file. */
    private array $usedCodes = [];

    protected function gate(): string
    {
        return 'create_products';
    }

    public function expectedColumns(): array
    {
        return [
            'product_name',
            'product_code',
            'product_barcode_symbology',
            'product_unit',
            'product_quantity',
            'product_cost',
            'product_price',
            'product_stock_alert',
            'product_order_tax',
            'product_tax_type',
            'product_note',
            'expiry_date',
            'category',
            'supplier',
        ];
    }

    protected function requiredHeaders(): array
    {
        return ['product_name', 'product_code'];
    }

    protected function prepareLookups(): void
    {
        $this->categoriesById = Category::all()->keyBy('id');
        $this->categoriesByName = Category::all()->keyBy(fn ($c) => strtolower((string) $c->category_name));
        $this->suppliersById = Supplier::all()->keyBy('id');
        $this->suppliersByName = Supplier::all()->keyBy(fn ($s) => strtolower((string) $s->supplier_name));
        $this->usedCodes = Product::pluck('product_code')->map(fn ($c) => (string) $c)->all();
    }

    protected function mapRow(array $raw, array &$errors): array
    {
        $attributes = [
            'product_name' => $raw['product_name'] ?? null,
            'product_code' => $raw['product_code'] ?? null,
            'product_barcode_symbology' => $raw['product_barcode_symbology'] ?? 'CODE128',
            'product_unit' => $raw['product_unit'] ?? null,
            'product_quantity' => $this->toInt($raw['product_quantity'] ?? null),
            'product_cost' => $this->toFloat($raw['product_cost'] ?? null),
            'product_price' => $this->toFloat($raw['product_price'] ?? null),
            'product_stock_alert' => $this->toInt($raw['product_stock_alert'] ?? null, 10),
            'product_order_tax' => $this->toInt($raw['product_order_tax'] ?? null, 0),
            'product_tax_type' => $this->toInt($raw['product_tax_type'] ?? null, 0),
            'product_note' => $raw['product_note'] ?? null,
            'expiry_date' => $this->normaliseDate($raw['expiry_date'] ?? null),
        ];

        // Duplicate product_code detection (existing products and within the file).
        $code = (string) ($attributes['product_code'] ?? '');
        if ($code !== '') {
            if (in_array($code, $this->usedCodes, true)) {
                $errors[] = (string) __('product.import_code_exists', ['code' => $code]);
            }
            $this->usedCodes[] = $code;
        }

        // Resolve category by id or name.
        $category = $raw['category'] ?? null;
        if ($category !== null && $category !== '') {
            $match = is_numeric($category)
                ? ($this->categoriesById[(int) $category] ?? null)
                : ($this->categoriesByName[strtolower($category)] ?? null);

            if ($match === null) {
                $errors[] = (string) __('product.import_category_unknown', ['value' => $category]);
            } else {
                $attributes['category_id'] = $match->id;
            }
        }

        // Resolve supplier by id or name.
        $supplier = $raw['supplier'] ?? null;
        if ($supplier !== null && $supplier !== '') {
            $match = is_numeric($supplier)
                ? ($this->suppliersById[(int) $supplier] ?? null)
                : ($this->suppliersByName[strtolower($supplier)] ?? null);

            if ($match === null) {
                $errors[] = (string) __('product.import_supplier_unknown', ['value' => $supplier]);
            } else {
                $attributes['supplier_id'] = $match->id;
            }
        }

        return $attributes;
    }

    protected function rowRules(): array
    {
        return [
            'product_name' => ['required', 'string', 'max:255'],
            'product_code' => ['required', 'numeric', 'max:2147483647'],
            'product_barcode_symbology' => ['required', 'string', 'max:255'],
            'product_unit' => ['required', 'string', 'max:255'],
            'product_quantity' => ['required', 'integer', 'min:1'],
            'product_cost' => ['required', 'numeric', 'max:2147483647'],
            'product_price' => ['required', 'numeric', 'max:2147483647'],
            'product_stock_alert' => ['required', 'integer', 'min:10'],
            'product_order_tax' => ['nullable', 'integer', 'min:0', 'max:100'],
            'product_tax_type' => ['nullable', 'integer'],
            'product_note' => ['nullable', 'string', 'max:1000'],
            'expiry_date' => ['nullable', 'date'],
            'category_id' => ['required', 'integer'],
            'supplier_id' => ['required', 'integer'],
        ];
    }

    protected function createRecord(array $attributes): void
    {
        Product::create($attributes);
    }

    public function redirectRouteName(): string
    {
        return 'products.index';
    }

    public function langPrefix(): string
    {
        return 'product';
    }

    public function previewColumns(): array
    {
        return [
            'product_name' => __('product.name'),
            'product_code' => __('product.code'),
            'product_quantity' => __('product.quantity'),
            'product_price' => __('product.price'),
        ];
    }

    public function title(): string
    {
        return __('import.products');
    }
}
