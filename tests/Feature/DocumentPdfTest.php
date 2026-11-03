<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use App\Services\Pdf\DocumentPdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\Concerns\SeedsStoreSettings;
use Tests\TestCase;

class DocumentPdfTest extends TestCase
{
    use RefreshDatabase;
    use SeedsStoreSettings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedStoreSettings();
    }

    private function userWith(array $permissions): User
    {
        $user = User::factory()->create();

        foreach ($permissions as $permission) {
            $user->givePermissionTo(Permission::findOrCreate($permission, 'web'));
        }

        return $user;
    }

    private function makeSale(): Sale
    {
        $customer = Customer::factory()->create();

        return Sale::create([
            'date' => now()->toDateString(),
            'customer_id' => $customer->id,
            'customer_name' => $customer->customer_name,
            'tax_percentage' => 0,
            'discount_percentage' => 0,
            'shipping_amount' => 0,
            'total_amount' => 0,
            'paid_amount' => 0,
            'due_amount' => 0,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'status' => 'Completed',
            'payment_status' => 'Paid',
            'payment_method' => 'Cash',
        ]);
    }

    public function test_guests_cannot_download_document_pdfs(): void
    {
        $sale = $this->makeSale();

        $this->get(route('sales.pdf', $sale->id))->assertRedirect();
    }

    public function test_pdf_downloads_respect_the_document_permission(): void
    {
        $sale = $this->makeSale();

        $this->actingAs($this->userWith(['access_sales']))
            ->get(route('sales.pdf', $sale->id))
            ->assertForbidden();

        $this->actingAs($this->userWith(['access_sales']))
            ->get(route('sales.pos.pdf', $sale->id))
            ->assertForbidden();

        foreach (['purchases.pdf', 'purchase-returns.pdf', 'sale-returns.pdf', 'quotations.pdf', 'purchases.withholding-certificate'] as $route) {
            $this->actingAs($this->userWith([]))->get(route($route, 1))->assertForbidden();
        }
    }

    public function test_an_authorised_user_gets_a_pdf_of_the_invoice(): void
    {
        $sale = $this->makeSale();

        $response = $this->actingAs($this->userWith(['show_sales']))
            ->get(route('sales.pdf', $sale->id))
            ->assertOk();

        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString('sale-'.$sale->reference.'.pdf', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_the_pos_receipt_is_rendered_as_pdf(): void
    {
        $sale = $this->makeSale();

        $response = $this->actingAs($this->userWith(['show_sales']))
            ->get(route('sales.pos.pdf', $sale->id))
            ->assertOk();

        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_missing_documents_and_non_numeric_ids_return_404(): void
    {
        $user = $this->userWith(['show_sales']);

        $this->actingAs($user)->get(route('sales.pdf', 999))->assertNotFound();
        $this->actingAs($user)->get('/sales/pdf/abc')->assertNotFound();
    }

    public function test_file_names_are_sanitised(): void
    {
        $service = app(DocumentPdfService::class);

        $this->assertSame('sale-INV-2026-001.pdf', $service->filename('sale-INV/2026/001'));
        $this->assertSame('a-b.pdf', $service->filename('a"b'));
        $this->assertSame('document.pdf', $service->filename('///'));
        $this->assertSame('report.pdf', $service->filename('report.pdf'));
    }
}
