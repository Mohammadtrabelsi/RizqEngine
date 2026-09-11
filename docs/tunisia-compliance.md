# Tunisian legal-compliance features — RizqEngine

This document covers the fiscal/legal features added for Tunisian law, beyond
the withholding-tax module documented in `docs/withholding-tax.md`.

## 1. Retenue à la source (RAS) on sales

The withholding-tax module now applies to **sales invoices** as well as
purchases. See the "Sales side" section of `docs/withholding-tax.md` for the
model, service and UI details. The net receivable from the customer is
`TTC − RAS`, tracked by `sales.due_amount`, and each applied RAS is snapshotted
in `sale_withholding_taxes`.

## 2. Legal, uninterrupted invoice numbering

Tunisian tax rules require sales invoices (factures) to carry a **continuous,
non-reusable** sequence. The previous scheme derived the number from
`max(id) + 1`, which races under concurrency and **re-issues** a number after a
deletion — not compliant.

- `document_sequences` — one monotonic counter row per document family
  (`key`, `prefix`, `next_number`). Ships seeded for `sale` (prefix `SL`),
  starting above any pre-existing invoice.
- `App\Services\DocumentNumberService::next($key, $prefix)` — allocates the next
  number under a **row lock** (`lockForUpdate`) inside the surrounding write
  transaction, so two concurrent invoices can never share a number and a
  deleted invoice's number is never reused.
- `Sale::creating` assigns the reference from this service; it is never taken
  from user input, and `SaleService::updateSale()` no longer overwrites it — the
  legal number is **immutable**.

The formatted reference keeps its existing shape (`SL-00007`) so historical data,
views and exports are unaffected.

## 3. Mandatory legal mentions on the invoice

`resources/views/sale/print.blade.php` now prints the legally-required mentions:

- **Matricule fiscal** of the company (`settings.company_tax_id`) and of the
  customer (`customers.tax_identification_number`), labelled `M.F.`.
- The full monetary breakdown: **Total HT**, remise (if any), **TVA** (rate and
  amount), **timbre fiscal**, **Total TTC**, each **retenue à la source** line,
  and the **net à payer**.
- A configurable **legal footer** (`settings.invoice_legal_mention`), e.g.
  registre de commerce, régime fiscal, or late-payment penalty wording.

Two settings drive this, editable under **Settings → General**:

- `settings.fiscal_stamp_amount` — the timbre fiscal (droit de timbre) per
  invoice, stored in dinars with three-decimal precision. `0`/null hides it.
- `settings.invoice_legal_mention` — free-text legal footer.

> Scope note: the app stores a single global VAT rate per document rather than a
> per-line ventilation, and the timbre fiscal is shown on the invoice as an
> additive legal line rather than being folded into the stored transactional
> total. Folding the timbre into the persisted TTC/payment balance is a larger
> change left as follow-up.

## 4. Déclaration de TVA (VAT return)

A new **VAT return** report (Reports → VAT Return,
`/vat-return-report`) computes, for a period (defaulting to the current month):

- **TVA collectée** — VAT on sales, net of sale returns, grouped by rate.
- **TVA déductible** — VAT on purchases, net of purchase returns, grouped by
  rate.
- **TVA à payer** = collectée − déductible; a negative result is shown as a
  **crédit de TVA** carried forward.

Implemented by `App\Services\Reports\VatReturnService` (grouping by the
document's `tax_percentage`) and `App\Livewire\Reports\VatReturnReport`. Guarded
by the existing `access_reports` permission.

## Tests

- `tests/Feature/WithholdingTaxSaleTest.php` — sales RAS: net/due, no-withholding
  regression, snapshot immutability after a master rate change.
- `tests/Feature/InvoiceSequentialNumberingTest.php` — continuous sequence,
  no reuse after deletion, immutable reference, generic allocator.
- `tests/Feature/VatReturnReportTest.php` — VAT due, VAT credit, period filter.

> As with the withholding module, `composer install` may be blocked in the
> authoring sandbox; run the suite in CI / locally:
> `php artisan test --filter "Withholding|Sequential|VatReturn"`.
