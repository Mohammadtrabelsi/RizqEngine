<?php

namespace Tests\Feature;

use App\Livewire\Products\ProductImport;
use App\Livewire\Products\ProductIndex;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\Spreadsheets\ProductExportService;
use App\Services\Spreadsheets\SpreadsheetExporter;
use App\Services\Tags\ProductTagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Tests\Concerns\SeedsStoreSettings;
use Tests\TestCase;

class SpreadsheetImportExportTest extends TestCase
{
    use RefreshDatabase;
    use SeedsStoreSettings;

    private function userWith(array $permissions): User
    {
        $user = User::factory()->create();

        foreach ($permissions as $permission) {
            $user->givePermissionTo(Permission::findOrCreate($permission, 'web'));
        }

        return $user;
    }

    /**
     * Build a real .xlsx upload from rows.
     *
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function xlsx(array $rows): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'xlsx_').'.xlsx';
        $writer = SimpleExcelWriter::create($path);
        $writer->addRows($rows);
        $writer->close();

        $contents = (string) file_get_contents($path);
        @unlink($path);

        return UploadedFile::fake()->createWithContent('products.xlsx', $contents);
    }

    public function test_products_and_tags_are_imported_from_an_xlsx_file(): void
    {
        $this->actingAs($this->userWith(['create_products']));
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $file = $this->xlsx([[
            'Product_Name' => 'Excel widget', // headers are case-insensitive
            'product_code' => 5001,
            'product_unit' => 'pcs',
            'product_quantity' => 3,
            'product_cost' => 2.5,
            'product_price' => 4,
            'product_stock_alert' => 10,
            'category' => $category->id,
            'supplier' => $supplier->id,
            'tags' => 'excel; promo',
        ]]);

        Livewire::test(ProductImport::class)
            ->set('file', $file)
            ->call('parse')
            ->assertHasNoErrors()
            ->assertSet('parsed', true)
            ->call('import');

        $product = Product::where('product_code', 5001)->firstOrFail();
        $this->assertSame('Excel widget', $product->product_name);
        $this->assertSame(2.5, (float) $product->product_cost);
        $this->assertEqualsCanonicalizing(['excel', 'promo'], $product->tags->pluck('name')->all());
    }

    public function test_invalid_rows_are_reported_with_their_line_number(): void
    {
        $this->actingAs($this->userWith(['create_products']));
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $csv = "product_name,product_code,product_unit,product_quantity,product_cost,product_price,product_stock_alert,category,supplier\n"
            ."Good,6001,pcs,5,2,4,10,{$category->id},{$supplier->id}\n"
            ."Bad,6002,pcs,5,2,4,10,999,{$supplier->id}\n";

        $component = Livewire::test(ProductImport::class)
            ->set('file', UploadedFile::fake()->createWithContent('import.csv', $csv))
            ->call('parse');

        $rows = $component->get('rows');
        $this->assertSame([], $rows[2]['errors']);
        $this->assertNotEmpty($rows[3]['errors']);

        $component->call('import');
        $this->assertDatabaseHas('products', ['product_code' => 6001]);
        $this->assertDatabaseMissing('products', ['product_code' => 6002]);
    }

    public function test_files_with_too_many_rows_are_rejected(): void
    {
        config(['documents.spreadsheets.import_max_rows' => 2]);
        $this->actingAs($this->userWith(['create_products']));

        $csv = "product_name,product_code\nA,1\nB,2\nC,3\n";

        Livewire::test(ProductImport::class)
            ->set('file', UploadedFile::fake()->createWithContent('import.csv', $csv))
            ->call('parse')
            ->assertHasErrors('file')
            ->assertSet('parsed', false);
    }

    public function test_disallowed_file_types_are_rejected(): void
    {
        $this->actingAs($this->userWith(['create_products']));

        Livewire::test(ProductImport::class)
            ->set('file', UploadedFile::fake()->createWithContent('payload.php', '<?php echo 1;'))
            ->call('parse')
            ->assertHasErrors(['file' => 'mimes']);
    }

    public function test_oversized_files_are_rejected(): void
    {
        config(['documents.spreadsheets.import_max_kb' => 1]);
        $this->actingAs($this->userWith(['create_products']));

        Livewire::test(ProductImport::class)
            ->set('file', UploadedFile::fake()->createWithContent('import.csv', "product_name,product_code\n".str_repeat("A,1\n", 1000)))
            ->call('parse')
            ->assertHasErrors(['file' => 'max']);
    }

    public function test_export_requires_the_export_permission(): void
    {
        $this->seedStoreSettings();
        $this->actingAs($this->userWith(['access_products']));

        Livewire::test(ProductIndex::class)
            ->call('export', 'csv')
            ->assertForbidden();
    }

    public function test_unknown_export_formats_are_refused(): void
    {
        $this->seedStoreSettings();
        $this->actingAs($this->userWith(['access_products', 'export_products']));

        Livewire::test(ProductIndex::class)
            ->call('export', 'php')
            ->assertNotFound();
    }

    public function test_export_downloads_the_filtered_products(): void
    {
        $this->seedStoreSettings();
        $this->actingAs($this->userWith(['access_products', 'export_products']));

        Product::factory()->create(['product_name' => 'Blue mug', 'product_code' => '7001']);
        Product::factory()->create(['product_name' => 'Red plate', 'product_code' => '7002']);

        Livewire::test(ProductIndex::class)
            ->set('search', 'Blue')
            ->call('export', 'xlsx')
            ->assertFileDownloaded();
    }

    public function test_export_rows_round_trip_the_import_columns(): void
    {
        $product = Product::factory()->create([
            'product_name' => 'Round trip',
            'product_code' => '8001',
            'product_cost' => 3.25,
            'expiry_date' => '2027-01-31',
        ]);
        app(ProductTagService::class)->sync($product, 'promo, seasonal');
        Product::factory()->create(['product_name' => 'Filtered out']);

        $rows = iterator_to_array(app(ProductExportService::class)->rows(['search' => 'Round']), false);

        $this->assertCount(1, $rows);
        $this->assertSame('Round trip', $rows[0]['product_name']);
        $this->assertSame(3.25, (float) $rows[0]['product_cost']);
        $this->assertSame('2027-01-31', $rows[0]['expiry_date']);
        $this->assertSame($product->category->category_name, $rows[0]['category']);
        $this->assertStringContainsString('promo', $rows[0]['tags']);

        // Every import column is present, so an export can be re-imported.
        $import = new ProductImport;
        $this->assertEqualsCanonicalizing($import->expectedColumns(), array_keys($rows[0]));
    }

    public function test_exported_csv_neutralises_formula_injection(): void
    {
        $path = app(SpreadsheetExporter::class)->write([
            ['name' => '=HYPERLINK("http://evil")', 'amount' => '-5', 'note' => '@SUM(A1)', 'plain' => 'ok'],
        ], 'csv');

        $row = SimpleExcelReader::create($path)->getRows()->first();
        @unlink($path);

        $this->assertSame("'=HYPERLINK(\"http://evil\")", $row['name']);
        $this->assertSame('-5', (string) $row['amount'], 'Negative numbers are not altered.');
        $this->assertSame("'@SUM(A1)", $row['note']);
        $this->assertSame('ok', $row['plain']);
    }

    public function test_exporter_rejects_unsupported_formats(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(SpreadsheetExporter::class)->write([], 'ods');
    }
}
