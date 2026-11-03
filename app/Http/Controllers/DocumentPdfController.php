<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Quotation;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Supplier;
use App\Services\Pdf\DocumentPdfService;
use App\Services\WithholdingCertificateService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

/**
 * PDF previews of sales documents. Each action enforces the same "show_*"
 * permission as the document's HTML detail page, so a printable copy is
 * never reachable by a user who cannot view the record itself.
 */
class DocumentPdfController extends Controller
{
    public function __construct(private readonly DocumentPdfService $pdf) {}

    public function sale(int $id): Response
    {
        abort_if(Gate::denies('show_sales'), 403);

        $sale = Sale::with(['saleDetails', 'withholdingTaxes'])->findOrFail($id);

        return $this->pdf->stream('sale.print', [
            'sale' => $sale,
            'customer' => Customer::findOrFail($sale->customer_id),
        ], 'sale-'.$sale->reference);
    }

    public function posReceipt(int $id): Response
    {
        abort_if(Gate::denies('show_sales'), 403);

        $sale = Sale::with('saleDetails')->findOrFail($id);

        return $this->pdf->stream('sale.print-pos', ['sale' => $sale], 'sale-'.$sale->reference, 'a7');
    }

    public function saleReturn(int $id): Response
    {
        abort_if(Gate::denies('show_sale_returns'), 403);

        $saleReturn = SaleReturn::with('saleReturnDetails')->findOrFail($id);

        return $this->pdf->stream('salesreturn.print', [
            'sale_return' => $saleReturn,
            'customer' => Customer::findOrFail($saleReturn->customer_id),
        ], 'sale-return-'.$saleReturn->reference);
    }

    public function purchase(int $id): Response
    {
        abort_if(Gate::denies('show_purchases'), 403);

        $purchase = Purchase::with('purchaseDetails')->findOrFail($id);

        return $this->pdf->stream('purchase.print', [
            'purchase' => $purchase,
            'supplier' => Supplier::findOrFail($purchase->supplier_id),
        ], 'purchase-'.$purchase->reference);
    }

    public function purchaseReturn(int $id): Response
    {
        abort_if(Gate::denies('show_purchase_returns'), 403);

        $purchaseReturn = PurchaseReturn::with('purchaseReturnDetails')->findOrFail($id);

        return $this->pdf->stream('purchasesreturn.print', [
            'purchase_return' => $purchaseReturn,
            'supplier' => Supplier::findOrFail($purchaseReturn->supplier_id),
        ], 'purchase-return-'.$purchaseReturn->reference);
    }

    public function quotation(int $id): Response
    {
        abort_if(Gate::denies('show_quotations'), 403);

        $quotation = Quotation::with('quotationDetails')->findOrFail($id);

        return $this->pdf->stream('quotation.print', [
            'quotation' => $quotation,
            'customer' => Customer::findOrFail($quotation->customer_id),
        ], 'quotation-'.$quotation->reference);
    }

    /**
     * Withholding-tax certificate (certificat de retenue à la source).
     */
    public function withholdingCertificate(int $id, WithholdingCertificateService $certificates): Response
    {
        abort_if(Gate::denies('show_purchases'), 403);

        $purchase = Purchase::with('withholdingTaxes')->findOrFail($id);

        abort_if($purchase->withholding_amount <= 0, 404);

        return $this->pdf->stream('withholding.certificate', [
            'certificate' => $certificates->forPurchase($purchase),
        ], 'withholding-certificate-'.$purchase->reference);
    }
}
