# Retenue à la source (Withholding Tax) — RizqEngine

This module adds Tunisian-style withholding tax (retenue à la source / RAS) to
RizqEngine. Withholding is **not** modelled as a VAT-style `Tax`: it is deducted
**after** the TTC to produce the net actually paid to the beneficiary.

```
Montant HT
+ TVA (+ autres taxes)
= Total TTC
- Retenue à la source (RAS)
= Net à payer
```

## Architecture

Everything mirrors the existing `Tax` module and app conventions.

| Layer | Class / file |
|-------|--------------|
| Enum | `App\Enums\WithholdingCalculationBase` (`ht`, `tva`, `ttc`) |
| Models | `App\Models\WithholdingTax`, `App\Models\PurchaseWithholdingTax` |
| Calculation engine | `App\Services\WithholdingTaxCalculator` |
| CRUD service | `App\Services\WithholdingTaxService` |
| Certificate | `App\Services\WithholdingCertificateService` |
| Admin UI | `App\Livewire\WithholdingTaxes\WithholdingTaxIndex` / `WithholdingTaxForm` |
| Invoice UI | integrated into the shared `App\Livewire\ProductCart` component |
| Persistence | integrated into `App\Services\PurchaseService` |

### The calculator is the single source of truth

`WithholdingTaxCalculator::calculate($withholdingTaxes, $ht, $tva, $ttc)` returns
the per-line breakdown, the total withheld and the net payable. **Both** the
live Livewire preview and the server-side persistence call it, so the figure the
user sees is exactly the figure that is stored. All monetary rounding happens
here and nowhere else (never in Blade or JavaScript).

## Tables

- `withholding_taxes` — master data (`name`, `code`, `rate`, `calculation_base`,
  `active`, `applicable_to_purchases`, `applicable_to_sales`, `start_date`,
  `end_date`).
- `purchase_withholding_taxes` — one immutable **snapshot** row per applied
  withholding, copying `name` / `code` / `rate` / `calculation_base` / taxable
  base / amount at invoice time.
- `purchases.withholding_amount` — total withheld on the purchase.
- `suppliers.subject_to_withholding`, `legal_form`, `fiscal_category` — the
  beneficiary's fiscal context (matricule fiscal already existed as
  `tax_identification_number`).
- `settings.company_tax_id` — the withholding party's matricule fiscal, for the
  certificate.

## Monetary precision

The rest of the app stores money as integer centimes (× 100). Because a RAS and
its certificate must keep the Tunisian dinar's **three decimals (millimes)**,
withholding amounts (`purchase_withholding_taxes.taxable_amount` / `amount` and
`purchases.withholding_amount`) are stored in **millimes (× 1000)** and exposed
through `/1000` accessors. `WithholdingTaxCalculator::SCALE = 3`.

## Calculation base

Configurable per withholding tax:

| Base | Example (HT 1000, TVA 190, TTC 1190) at 3% |
|------|---------------------------------------------|
| `ht`  | 1000 × 3% = **30.000** → net 1160.000 |
| `tva` | 190 × 3% = 5.700 |
| `ttc` | 1190 × 3% = **35.700** → net 1154.300 |

## Snapshots & history

When a withholding is applied, its rate/base/amount are copied into
`purchase_withholding_taxes`. Editing the master `WithholdingTax` rate later does
**not** change historical purchases (covered by a test).

Deleting a withholding tax that has ever been applied is refused; the service
deactivates it instead (`WithholdingTaxService::deleteOrDeactivate`).

## Payment / accounting

The supplier is owed the **net** (TTC − RAS), so `purchases.due_amount` tracks
the net, not the gross. The purchase therefore retains:

- gross: `total_amount` (TTC)
- withheld: `withholding_amount`
- net payable: `net_payable` accessor (= TTC − RAS)
- paid: `paid_amount`, remaining: `due_amount`

With no withholding selected the net equals the TTC, so existing purchases are
unaffected.

## Certificate

`GET /purchases/{id}/withholding-certificate` streams a PDF
(`resources/views/withholding/certificate.blade.php`) via the app's existing
DomPDF setup. It carries the withholding party (company + matricule), the
beneficiary (supplier + matricule), the invoice reference/date, the per-line
base/rate/amount, and the net paid. The structure is intentionally extensible
for further legally-required Tunisian fields.

## Permissions & navigation

Permissions: `access_withholding_taxes`, `create_withholding_taxes`,
`edit_withholding_taxes`, `delete_withholding_taxes` (granted to Owner / Manager
/ Admin). The admin CRUD lives under **Settings → Withholding taxes**
(`/withholding-taxes`).

## UI

On the purchase create/edit form the shared `ProductCart` component shows a
**Retenue à la source** section listing the effective purchase-applicable
withholding taxes as checkboxes (zero, one, or many). The totals card updates
live to show Total TTC → each RAS line → Net à payer, and the selected ids are
posted as `withholding_tax_ids[]`.

## Tunisian rates — IMPORTANT

**No rate is hardcoded.** The `withholding_taxes` table ships empty; the
administrator configures rates. Tunisian RAS rates depend on the nature of the
operation and the beneficiary's fiscal regime and change over time, so:

- enter rates from official/legally reliable sources and record the source and
  validity date (use `description` + `start_date`/`end_date`);
- common rates seen in practice (**verify before use, not authoritative**):
  1%, 1.5%, 3%, 5%, 10%, 15%;
- when a rule is uncertain, make it configurable rather than coding it as truth.

## Tests

- `tests/Unit/WithholdingTaxCalculatorTest.php` — pure calculator: 3% on TTC
  (35.700 / net 1154.300), 3% on HT (30 / net 1160), no withholding, multiple
  withholdings, three-decimal rounding.
- `tests/Feature/WithholdingTaxPurchaseTest.php` — persistence: net/due from RAS,
  no-withholding regression, multiple lines, and snapshot immutability after a
  master rate change.

> Note: `composer install` could not complete in the authoring sandbox (GitHub
> egress blocked), so the suite must be run in CI / locally:
> `php artisan test --filter Withholding`.

## Sales side (facture client)

RAS is now wired on the **sale (facture client)** side too, mirroring the
purchase integration:

- `sale_withholding_taxes` — one immutable snapshot row per applied withholding
  (same columns as `purchase_withholding_taxes`).
- `sales.withholding_amount` — total withheld on the sale (millimes × 1000).
- `Sale::withholdingTaxes()` + `Sale::net_payable` accessor (= TTC − RAS).
- `SaleService::createSale()` / `updateSale()` compute and persist the RAS via
  the same `WithholdingTaxCalculator`; `sales.due_amount` tracks the **net**.
- The shared `ProductCart` RAS selector is shown on the sale form, listing the
  sale-applicable withholding taxes (`WithholdingTax::scopeForSide('sale')`).
- Applied sale withholdings are printed on the invoice (see below) and block
  deletion of the master tax (`WithholdingTax::isUsed()` now checks both sides).

> Precision note: the per-line snapshot models
> (`Purchase/SaleWithholdingTax`) store amounts in **millimes (× 1000)** and now
> expose them through `/1000` accessors. A prior `/100` divisor on
> `PurchaseWithholdingTax` (which returned 10× the real value) was corrected.

## Related Tunisian-compliance features

Shipped alongside sales RAS (see `docs/tunisia-compliance.md`):

- **Legal invoice numbering** — sales references come from a gapless,
  non-reusable counter (`document_sequences` + `DocumentNumberService`) and are
  immutable.
- **Legal invoice mentions** — matricule fiscal of both parties, HT / TVA /
  timbre fiscal / TTC / RAS / net à payer, and a configurable legal footer.
- **VAT return** — the *Déclaration de TVA* report (collected vs deductible VAT
  by rate, net VAT due / credit).
