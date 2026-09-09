<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\Setting;
use App\Models\Supplier;

/**
 * Assembles the data for a withholding-tax certificate (certificat de retenue
 * à la source) from a purchase and its snapshotted withholding lines.
 *
 * The certificate identifies the party performing the withholding (the
 * company), the beneficiary (the supplier), and the amounts retained. The
 * returned structure is intentionally extensible so the fields legally
 * required on the Tunisian certificate can be added without changing callers.
 */
class WithholdingCertificateService
{
    /**
     * Build the certificate payload for a purchase.
     *
     * @return array<string, mixed>
     */
    public function forPurchase(Purchase $purchase): array
    {
        /** @var Setting|null $settings */
        $settings = Setting::first();
        $supplier = Supplier::find($purchase->supplier_id);

        $lines = $purchase->withholdingTaxes->map(fn ($line) => [
            'name' => $line->name,
            'code' => $line->code,
            'rate' => (float) $line->rate,
            'calculation_base' => $line->calculation_base->value,
            'taxable_amount' => (float) $line->taxable_amount,
            'amount' => (float) $line->amount,
        ])->all();

        return [
            // Party performing the withholding (l'organisme qui retient).
            'company' => [
                'name' => $settings->company_name ?? '',
                'address' => $settings->company_address ?? '',
                'tax_id' => $settings->company_tax_id ?? '',
            ],
            // Beneficiary of the payment (le bénéficiaire).
            'beneficiary' => [
                'name' => $purchase->supplier_name,
                'tax_id' => $supplier?->tax_identification_number ?? '',
                'legal_form' => $supplier?->legal_form ?? '',
                'fiscal_category' => $supplier?->fiscal_category ?? '',
            ],
            'invoice' => [
                'reference' => $purchase->reference,
                'date' => $purchase->date,
            ],
            'lines' => $lines,
            'total_ttc' => (float) $purchase->total_amount,
            'withholding_total' => (float) $purchase->withholding_amount,
            'net_paid' => (float) $purchase->net_payable,
            'withholding_date' => $purchase->date,
        ];
    }
}
