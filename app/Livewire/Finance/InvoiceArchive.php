<?php

namespace App\Livewire\Finance;

use App\Services\Finance\InvoiceArchiveService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceArchive extends Component
{
    #[Url]
    public ?string $start_date = null;

    #[Url]
    public ?string $end_date = null;

    #[Url]
    public string $type = InvoiceArchiveService::TYPE_ALL;

    public function downloadZip(InvoiceArchiveService $archive)
    {
        $path = $archive->buildZip($this->start_date, $this->end_date, $this->type);

        if (! $path) {
            session()->flash('warning', __('finance.no_documents'));

            return null;
        }

        return response()->download($path, basename($path))->deleteFileAfterSend(true);
    }

    public function downloadCsv(InvoiceArchiveService $archive): StreamedResponse
    {
        $csv = $archive->buildCsv($this->start_date, $this->end_date, $this->type);
        $filename = 'invoices-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function render(InvoiceArchiveService $archive)
    {
        $documents = $archive->documents($this->start_date, $this->end_date, $this->type);

        return view('livewire.finance.invoice-archive', [
            'documents' => $documents,
            'total' => $documents->sum('amount'),
        ])->layout('components.layouts.admin', ['title' => __('finance.invoice_archive')]);
    }
}
