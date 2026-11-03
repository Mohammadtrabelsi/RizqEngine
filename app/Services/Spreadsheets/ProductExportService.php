<?php

namespace App\Services\Spreadsheets;

use App\Models\Product;
use App\Services\ProductCatalogService;
use Generator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Exports the (filtered) product catalogue to CSV / XLSX.
 *
 * Columns mirror the product import template, so an exported file can be
 * edited and re-imported. Products are read in chunks with lazy(), keeping
 * memory flat regardless of catalogue size.
 */
class ProductExportService
{
    public function __construct(
        private readonly ProductCatalogService $catalog,
        private readonly SpreadsheetExporter $exporter,
    ) {}

    /**
     * @param  array<string, mixed>  $filters  Same filters as the product index.
     */
    public function download(array $filters, string $format = 'csv'): BinaryFileResponse
    {
        return $this->exporter->download(
            $this->rows($filters),
            'products-'.now()->format('Ymd-His'),
            $format,
        );
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Generator<int, array<string, mixed>>
     */
    public function rows(array $filters): Generator
    {
        $dateFormat = (string) config('documents.spreadsheets.export_date_format', 'Y-m-d');

        $products = $this->catalog->indexQuery($filters)
            ->with('tags')
            ->lazyById(500);

        foreach ($products as $product) {
            /** @var Product $product */
            yield [
                'product_name' => $product->product_name,
                'product_code' => $product->product_code,
                'product_barcode_symbology' => $product->product_barcode_symbology,
                'product_unit' => $product->product_unit,
                'product_quantity' => $product->product_quantity,
                'product_cost' => $product->product_cost,
                'product_price' => $product->product_price,
                'product_stock_alert' => $product->product_stock_alert,
                'product_order_tax' => $product->product_order_tax,
                'product_tax_type' => $product->product_tax_type,
                'product_note' => $product->product_note,
                'expiry_date' => $product->expiry_date?->format($dateFormat),
                'category' => $product->category?->category_name,
                'supplier' => $product->supplier?->supplier_name,
                'tags' => $product->tags->pluck('name')->implode(', '),
            ];
        }
    }
}
