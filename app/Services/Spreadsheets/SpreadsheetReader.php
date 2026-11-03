<?php

namespace App\Services\Spreadsheets;

use DateTimeInterface;
use Illuminate\Support\LazyCollection;
use Spatie\SimpleExcel\SimpleExcelReader;

/**
 * Streams rows out of an uploaded CSV or XLSX file (spatie/simple-excel).
 *
 * Rows are yielded lazily, so even a large file is never fully loaded into
 * memory. Headers are normalised (trimmed, lower-cased) and every cell is
 * returned as a trimmed string — or null when empty — so import mappers can
 * treat CSV and Excel input identically.
 */
class SpreadsheetReader
{
    /**
     * @param  string  $path  Absolute path to the file on disk.
     * @param  string  $extension  Real file type ("csv", "txt" or "xlsx").
     */
    public function open(string $path, string $extension): SimpleExcelReader
    {
        $type = strtolower($extension) === 'xlsx' ? 'xlsx' : 'csv';

        return SimpleExcelReader::create($path, $type)
            ->formatHeadersUsing(fn ($header) => strtolower(trim((string) $header)));
    }

    /**
     * Normalised header row of the file.
     *
     * @return array<int, string>
     */
    public function headers(string $path, string $extension): array
    {
        return array_values(array_filter(
            $this->open($path, $extension)->getHeaders() ?? [],
            fn ($header) => $header !== '',
        ));
    }

    /**
     * Data rows as [header => string|null], skipping fully blank rows.
     *
     * @return LazyCollection<int, array<string, string|null>>
     */
    public function rows(string $path, string $extension): LazyCollection
    {
        return $this->open($path, $extension)
            ->getRows()
            ->map(fn (array $row) => array_map(fn ($value) => $this->cell($value), $row))
            ->reject(fn (array $row) => count(array_filter($row, fn ($value) => $value !== null)) === 0);
    }

    /**
     * Convert a raw cell (string, number, bool or date from Excel) to a
     * trimmed string, or null when empty.
     */
    private function cell(mixed $value): ?string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
