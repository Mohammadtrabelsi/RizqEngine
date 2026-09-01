<?php

namespace App\Services\Finance;

use App\Models\FixedPayment;
use App\Models\Outing;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

/**
 * Unifies the two document sources — outing vouchers and fixed-payment invoices
 * — into a single filterable/exportable record set for the invoice archive.
 * Supports period + type filtering, ZIP bulk archiving of the stored files and
 * a CSV summary export.
 */
class InvoiceArchiveService
{
    public const TYPE_ALL = 'all';

    public const TYPE_OUTINGS = 'outings';

    public const TYPE_FIXED = 'fixed';

    /**
     * Return the unified, filtered document list newest-first. Each entry is a
     * plain array: type, reference, date (Carbon), description, amount, path.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function documents(?string $start, ?string $end, string $type = self::TYPE_ALL): Collection
    {
        $startDate = $start ? Carbon::parse($start)->startOfDay() : null;
        $endDate = $end ? Carbon::parse($end)->endOfDay() : null;

        $documents = collect();

        if ($type === self::TYPE_ALL || $type === self::TYPE_OUTINGS) {
            Outing::query()
                ->when($startDate, fn ($q) => $q->where('date', '>=', $startDate))
                ->when($endDate, fn ($q) => $q->where('date', '<=', $endDate))
                ->orderByDesc('date')
                ->get()
                ->each(function (Outing $outing) use ($documents) {
                    $documents->push([
                        'type' => self::TYPE_OUTINGS,
                        'reference' => $outing->reference,
                        'date' => $outing->date,
                        'description' => trim(($outing->location ?? '').' — '.($outing->purpose ?? ''), ' —'),
                        'amount' => $outing->total(),
                        'path' => $outing->voucher_path,
                    ]);
                });
        }

        if ($type === self::TYPE_ALL || $type === self::TYPE_FIXED) {
            FixedPayment::query()
                ->with('monthlyBudget')
                ->when($startDate, fn ($q) => $q->where('due_date', '>=', $startDate))
                ->when($endDate, fn ($q) => $q->where('due_date', '<=', $endDate))
                ->get()
                ->each(function (FixedPayment $payment) use ($documents) {
                    $documents->push([
                        'type' => self::TYPE_FIXED,
                        'reference' => 'FP-'.$payment->id,
                        'date' => $payment->due_date ?? $payment->created_at,
                        'description' => $payment->label,
                        'amount' => (float) $payment->amount,
                        'path' => $payment->invoice_path,
                    ]);
                });
        }

        return $documents
            ->sortByDesc(fn ($doc) => optional($doc['date'])->timestamp ?? 0)
            ->values();
    }

    /**
     * Compile every stored file matching the filter into a ZIP archive on the
     * local disk and return its absolute path.
     *
     * @return string|null  the archive path, or null when nothing was archived
     */
    public function buildZip(?string $start, ?string $end, string $type = self::TYPE_ALL): ?string
    {
        $documents = $this->documents($start, $end, $type)
            ->filter(fn ($doc) => $doc['path'] && Storage::disk('public')->exists($doc['path']));

        if ($documents->isEmpty()) {
            return null;
        }

        Storage::disk('local')->makeDirectory('finance-archives');
        $absolute = Storage::disk('local')->path('finance-archives/invoices-'.now()->format('Ymd-His').'.zip');

        $zip = new ZipArchive;
        if ($zip->open($absolute, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return null;
        }

        foreach ($documents as $doc) {
            $contents = Storage::disk('public')->get($doc['path']);
            $extension = pathinfo($doc['path'], PATHINFO_EXTENSION) ?: 'pdf';
            $zip->addFromString($doc['type'].'/'.$doc['reference'].'.'.$extension, $contents);
        }

        $zip->close();

        return $absolute;
    }

    /**
     * Build a CSV summary of the filtered documents and return its raw contents.
     */
    public function buildCsv(?string $start, ?string $end, string $type = self::TYPE_ALL): string
    {
        $documents = $this->documents($start, $end, $type);

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Type', 'Reference', 'Date', 'Description', 'Amount', 'File']);

        foreach ($documents as $doc) {
            fputcsv($handle, [
                $doc['type'],
                $doc['reference'],
                optional($doc['date'])->format('Y-m-d'),
                $doc['description'],
                number_format($doc['amount'], 2, '.', ''),
                $doc['path'] ?? '',
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
