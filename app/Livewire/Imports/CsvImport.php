<?php

namespace App\Livewire\Imports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Reusable full-page Livewire component for bulk-importing records from a CSV.
 *
 * The flow is two steps: the user uploads a CSV, the component parses and
 * validates every row (showing a preview with per-row errors), then confirms
 * and only the valid rows are persisted inside a single transaction.
 *
 * Concrete imports (products, customers, suppliers) extend this class and
 * describe their columns, validation, row mapping and persistence.
 */
abstract class CsvImport extends Component
{
    use WithFileUploads;

    /** Uploaded CSV file. */
    #[Validate('required|file|mimes:csv,txt|max:5120')]
    public $file;

    /**
     * Parsed rows keyed by their (1-based) line number.
     *
     * @var array<int, array{attributes: array<string, mixed>, errors: array<int, string>}>
     */
    public array $rows = [];

    /** Whether the uploaded file has been parsed and is ready for preview. */
    public bool $parsed = false;

    public function mount(): void
    {
        abort_if(Gate::denies($this->gate()), 403);
    }

    /** The permission gate guarding this import. */
    abstract protected function gate(): string;

    /**
     * Expected CSV column names (lowercased). Columns outside this set are ignored.
     *
     * @return array<int, string>
     */
    abstract public function expectedColumns(): array;

    /**
     * Columns that MUST be present in the header for the file to be accepted.
     *
     * @return array<int, string>
     */
    abstract protected function requiredHeaders(): array;

    /**
     * Map a raw CSV row (column => value) to model attributes. Resolution
     * errors (e.g. an unknown foreign key) should be appended to $errors.
     *
     * @param  array<string, string|null>  $raw
     * @param  array<int, string>  $errors
     * @return array<string, mixed>
     */
    abstract protected function mapRow(array $raw, array &$errors): array;

    /**
     * Validation rules applied to each mapped row.
     *
     * @return array<string, mixed>
     */
    abstract protected function rowRules(): array;

    /**
     * Persist a single valid row and return the created model.
     *
     * @param  array<string, mixed>  $attributes
     */
    abstract protected function createRecord(array $attributes): void;

    /** Route name to redirect to after a successful import (also used by the view's "back" link). */
    abstract public function redirectRouteName(): string;

    /**
     * Translation key prefix (e.g. "product", "customer") used for the shared
     * view's labels and flash messages.
     */
    abstract public function langPrefix(): string;

    /**
     * Columns shown in the preview table as [attribute => translated label].
     *
     * @return array<string, string>
     */
    abstract public function previewColumns(): array;

    /** Page/heading title. */
    abstract public function title(): string;

    /**
     * Hook for subclasses to pre-load lookup data before parsing rows. Runs once
     * per parse. Return value is ignored; store state on the instance instead.
     */
    protected function prepareLookups(): void {}

    /**
     * Re-parse whenever a new file is selected so the preview stays in sync.
     */
    public function updatedFile(): void
    {
        $this->reset(['rows', 'parsed']);
        $this->resetValidation();
    }

    /**
     * Parse the uploaded CSV, mapping and validating each data row.
     */
    public function parse(): void
    {
        abort_if(Gate::denies($this->gate()), 403);

        $this->validate();

        $handle = fopen($this->file->getRealPath(), 'r');

        if ($handle === false) {
            $this->addError('file', __('import.read_failed'));

            return;
        }

        $header = fgetcsv($handle);

        if ($header === false || $header === null) {
            fclose($handle);
            $this->addError('file', __('import.empty_file'));

            return;
        }

        $header = array_map(fn ($column) => strtolower(trim((string) $column)), $header);

        foreach ($this->requiredHeaders() as $required) {
            if (! in_array($required, $header, true)) {
                fclose($handle);
                $this->addError('file', __('import.missing_columns', [
                    'columns' => implode(', ', $this->requiredHeaders()),
                ]));

                return;
            }
        }

        $this->prepareLookups();

        $expected = $this->expectedColumns();
        $rows = [];
        $line = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $line++;

            // Skip fully blank lines.
            if (count(array_filter($data, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $raw = [];
            foreach ($header as $index => $column) {
                if (in_array($column, $expected, true)) {
                    $raw[$column] = isset($data[$index]) ? trim((string) $data[$index]) : null;
                }
            }

            $errors = [];
            $attributes = $this->mapRow($raw, $errors);
            $errors = array_merge($errors, $this->validateRow($attributes));

            $rows[$line] = [
                'attributes' => $attributes,
                'errors' => $errors,
            ];
        }

        fclose($handle);

        $this->rows = $rows;
        $this->parsed = true;
    }

    /**
     * Persist every valid row. Rows with errors are skipped.
     */
    public function import(): void
    {
        abort_if(Gate::denies($this->gate()), 403);

        if (! $this->parsed) {
            return;
        }

        $valid = array_filter($this->rows, fn ($row) => count($row['errors']) === 0);

        if (count($valid) === 0) {
            session()->flash('warning', __('import.nothing_valid'));

            return;
        }

        DB::transaction(function () use ($valid) {
            foreach ($valid as $row) {
                $this->createRecord($row['attributes']);
            }
        });

        session()->flash('success', __('import.success', ['count' => count($valid)]));

        $this->redirectRoute($this->redirectRouteName(), navigate: true);
    }

    /**
     * Validate a single mapped row.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<int, string>
     */
    protected function validateRow(array $attributes): array
    {
        return Validator::make($attributes, $this->rowRules())->errors()->all();
    }

    /** Count of valid rows in the current preview. */
    public function getValidCountProperty(): int
    {
        return count(array_filter($this->rows, fn ($row) => count($row['errors']) === 0));
    }

    /** Count of rows with at least one validation error. */
    public function getInvalidCountProperty(): int
    {
        return count(array_filter($this->rows, fn ($row) => count($row['errors']) > 0));
    }

    protected function toInt(?string $value, int $default = 0): int
    {
        $value = trim((string) $value);

        return $value === '' ? $default : (int) $value;
    }

    protected function toFloat(?string $value, float $default = 0.0): float
    {
        $value = trim((string) $value);

        if ($value === '') {
            return $default;
        }

        // Accept comma as a decimal separator.
        return (float) str_replace(',', '.', $value);
    }

    /** Normalise a date string to Y-m-d, or null when empty. */
    protected function normaliseDate(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $timestamp = strtotime($value);

        return $timestamp === false ? $value : date('Y-m-d', $timestamp);
    }

    public function render()
    {
        return view('livewire.imports.csv-import')
            ->layout('components.layouts.admin', ['title' => $this->title()]);
    }
}
