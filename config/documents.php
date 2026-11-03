<?php

/*
|--------------------------------------------------------------------------
| Business documents: PDF rendering and spreadsheet import / export
|--------------------------------------------------------------------------
|
| Technical defaults only. Company identity on documents (name, logo,
| address, legal footer) is managed in the admin Settings screen and is not
| duplicated here.
|
*/

return [

    'pdf' => [
        // Default paper size and orientation for A4 documents (invoices,
        // purchases, quotations, returns). POS receipts use their own size.
        'paper' => env('DOCUMENTS_PDF_PAPER', 'a4'),
        'orientation' => env('DOCUMENTS_PDF_ORIENTATION', 'portrait'),
    ],

    'spreadsheets' => [
        // File types accepted by the bulk import screens.
        'import_extensions' => ['csv', 'txt', 'xlsx'],

        // Maximum accepted upload size, in kilobytes.
        'import_max_kb' => (int) env('IMPORT_MAX_KB', 5120),

        // Maximum number of data rows parsed from a single import file. Rows
        // beyond the limit are rejected so a huge file cannot exhaust memory
        // while building the preview.
        'import_max_rows' => (int) env('IMPORT_MAX_ROWS', 5000),

        // Formats offered by export actions.
        'export_formats' => ['csv', 'xlsx'],

        // Date format used for date columns in exported files.
        'export_date_format' => env('EXPORT_DATE_FORMAT', 'Y-m-d'),
    ],

];
