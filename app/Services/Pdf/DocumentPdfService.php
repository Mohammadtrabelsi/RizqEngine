<?php

namespace App\Services\Pdf;

use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Response;

/**
 * Single entry point for rendering business documents (invoices, purchases,
 * quotations, returns, certificates) to PDF.
 *
 * Wraps the already-installed DomPDF renderer so every document shares the
 * same paper defaults and file naming, and so controllers stay free of
 * rendering details. Templates live in resources/views and are plain Blade.
 */
class DocumentPdfService
{
    /**
     * Render a Blade view to a PDF document.
     *
     * @param  array<string, mixed>  $data
     */
    public function render(string $view, array $data = [], ?string $paper = null, ?string $orientation = null): PDF
    {
        /** @var PDF $pdf A fresh renderer per document (the binding is not shared). */
        $pdf = app('dompdf.wrapper');

        return $pdf->loadView($view, $data)->setPaper(
            $paper ?? (string) config('documents.pdf.paper', 'a4'),
            $orientation ?? (string) config('documents.pdf.orientation', 'portrait'),
        );
    }

    /**
     * Render a view and stream it inline to the browser (preview).
     *
     * @param  array<string, mixed>  $data
     */
    public function stream(string $view, array $data, string $filename, ?string $paper = null): Response
    {
        return $this->render($view, $data, $paper)->stream($this->filename($filename));
    }

    /**
     * Render a view and send it as a file download.
     *
     * @param  array<string, mixed>  $data
     */
    public function download(string $view, array $data, string $filename, ?string $paper = null): Response
    {
        return $this->render($view, $data, $paper)->download($this->filename($filename));
    }

    /**
     * Normalise a document file name: strip characters that are unsafe in a
     * Content-Disposition header and make sure it ends with ".pdf".
     */
    public function filename(string $name): string
    {
        $name = preg_replace('/[^A-Za-z0-9._-]+/', '-', $name) ?? 'document';
        $name = trim($name, '-.');

        if ($name === '') {
            $name = 'document';
        }

        return str_ends_with(strtolower($name), '.pdf') ? $name : $name.'.pdf';
    }
}
