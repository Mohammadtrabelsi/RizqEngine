<?php

namespace App\Services\Reports;

use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SaleReturn;
use Illuminate\Database\Eloquent\Builder;

/**
 * Builds the VAT return (déclaration de TVA) for a period.
 *
 * Tunisian VAT is declared monthly: the taxable person pays the difference
 * between the VAT collected on sales (TVA collectée) and the VAT paid on
 * purchases (TVA déductible). Sale returns reduce the collected VAT and
 * purchase returns reduce the deductible VAT.
 *
 *     TVA à payer = TVA collectée − TVA déductible
 *
 * A negative result is a VAT credit (crédit de TVA) carried to the next period.
 *
 * The app stores a single global tax rate and VAT amount per document (not a
 * per-line ventilation), so the breakdown is grouped by that document rate.
 * All monetary columns are integer cents and are scaled back to major units.
 */
class VatReturnService
{
    /**
     * Compute the full VAT return for the given inclusive date range.
     *
     * @return array{
     *     collected: array<int, array{rate: float, base: float, vat: float}>,
     *     deductible: array<int, array{rate: float, base: float, vat: float}>,
     *     collected_total: float,
     *     deductible_total: float,
     *     vat_due: float,
     *     is_credit: bool,
     * }
     */
    public function build(string $startDate, string $endDate): array
    {
        $collected = $this->collected($startDate, $endDate);
        $deductible = $this->deductible($startDate, $endDate);

        $collectedTotal = round(array_sum(array_column($collected, 'vat')), 3);
        $deductibleTotal = round(array_sum(array_column($deductible, 'vat')), 3);
        $vatDue = round($collectedTotal - $deductibleTotal, 3);

        return [
            'collected' => $collected,
            'deductible' => $deductible,
            'collected_total' => $collectedTotal,
            'deductible_total' => $deductibleTotal,
            'vat_due' => $vatDue,
            'is_credit' => $vatDue < 0,
        ];
    }

    /**
     * VAT collected on sales, net of sale returns, grouped by rate.
     *
     * @return array<int, array{rate: float, base: float, vat: float}>
     */
    private function collected(string $startDate, string $endDate): array
    {
        $sales = $this->groupByRate(
            Sale::query()->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate)
        );

        $returns = $this->groupByRate(
            SaleReturn::query()->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate)
        );

        return $this->netByRate($sales, $returns);
    }

    /**
     * VAT paid on purchases, net of purchase returns, grouped by rate.
     *
     * @return array<int, array{rate: float, base: float, vat: float}>
     */
    private function deductible(string $startDate, string $endDate): array
    {
        $purchases = $this->groupByRate(
            Purchase::query()->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate)
        );

        $returns = $this->groupByRate(
            PurchaseReturn::query()->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate)
        );

        return $this->netByRate($purchases, $returns);
    }

    /**
     * Sum VAT amount and taxable base per tax rate for a document query.
     * The taxable base is TTC − VAT (i.e. the HT amount that bore the VAT).
     *
     * @param  Builder<covariant \Illuminate\Database\Eloquent\Model>  $query
     * @return array<string, array{rate: float, base: float, vat: float}>
     */
    private function groupByRate(Builder $query): array
    {
        $rows = $query
            ->where('tax_amount', '>', 0)
            ->selectRaw('tax_percentage as rate, COALESCE(SUM(tax_amount), 0) as vat, COALESCE(SUM(total_amount - tax_amount), 0) as base')
            ->groupBy('tax_percentage')
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $rate = (float) $row->getAttribute('rate');
            $result[(string) $rate] = [
                'rate' => $rate,
                'vat' => $row->getAttribute('vat') / 100,
                'base' => $row->getAttribute('base') / 100,
            ];
        }

        return $result;
    }

    /**
     * Subtract the returns bucket from the documents bucket per rate.
     *
     * @param  array<string, array{rate: float, base: float, vat: float}>  $documents
     * @param  array<string, array{rate: float, base: float, vat: float}>  $returns
     * @return array<int, array{rate: float, base: float, vat: float}>
     */
    private function netByRate(array $documents, array $returns): array
    {
        foreach ($returns as $rate => $bucket) {
            if (! isset($documents[$rate])) {
                $documents[$rate] = ['rate' => $bucket['rate'], 'base' => 0.0, 'vat' => 0.0];
            }
            $documents[$rate]['base'] = round($documents[$rate]['base'] - $bucket['base'], 3);
            $documents[$rate]['vat'] = round($documents[$rate]['vat'] - $bucket['vat'], 3);
        }

        $rows = array_values($documents);
        usort($rows, fn ($a, $b) => $a['rate'] <=> $b['rate']);

        return $rows;
    }
}
