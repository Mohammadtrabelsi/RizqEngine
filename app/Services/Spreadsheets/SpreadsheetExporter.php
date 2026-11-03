<?php

namespace App\Services\Spreadsheets;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Writes rows to a CSV or XLSX file and returns it as a download
 * (spatie/simple-excel).
 *
 * Rows are written one at a time from any iterable — pass a lazy query
 * (Builder::lazy()) or a generator so exports of any size run in constant
 * memory. The file is written to the system temp directory (never a public
 * disk) and deleted as soon as it has been sent.
 */
class SpreadsheetExporter
{
    /**
     * @param  iterable<array<string, mixed>>  $rows  Associative rows; the keys of the first row become the header.
     * @param  string  $basename  File name without extension.
     */
    public function download(iterable $rows, string $basename, string $format = 'csv'): BinaryFileResponse
    {
        $format = $this->format($format);
        $path = $this->write($rows, $format);

        $filename = Str::slug($basename) ?: 'export';

        return response()
            ->download($path, $filename.'.'.$format, [
                'Content-Type' => $format === 'xlsx'
                    ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    : 'text/csv; charset=UTF-8',
            ])
            ->deleteFileAfterSend();
    }

    /**
     * Write the rows to a new temporary file and return its path.
     *
     * @param  iterable<array<string, mixed>>  $rows
     */
    public function write(iterable $rows, string $format = 'csv'): string
    {
        $format = $this->format($format);
        $temp = tempnam(sys_get_temp_dir(), 'export_');
        @unlink($temp);
        $path = $temp.'.'.$format;

        $writer = SimpleExcelWriter::create($path, $format);

        foreach ($rows as $row) {
            $writer->addRow(array_map(fn ($value) => $this->sanitize($value), $row));
        }

        $writer->close();

        return $path;
    }

    /**
     * Neutralise spreadsheet formula injection: a text cell starting with
     * "=", "+", "-", "@" or a control character would be evaluated as a
     * formula by Excel / LibreOffice, so it is prefixed with a quote.
     * Plain numbers (including negatives) are left untouched.
     */
    public function sanitize(mixed $value): mixed
    {
        if (! is_string($value) || $value === '' || is_numeric($value)) {
            return $value;
        }

        return in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true) ? "'".$value : $value;
    }

    /**
     * Validate a requested export format against the configured whitelist.
     */
    public function format(string $format): string
    {
        $format = strtolower($format);

        if (! in_array($format, (array) config('documents.spreadsheets.export_formats', ['csv', 'xlsx']), true)) {
            throw new InvalidArgumentException("Unsupported export format [{$format}].");
        }

        return $format;
    }
}
