{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        {{-- Filters --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('finance.start_date') }}</label>
                        <input type="date" wire:model.live="start_date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('finance.end_date') }}</label>
                        <input type="date" wire:model.live="end_date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('finance.type') }}</label>
                        <select wire:model.live="type" class="form-select">
                            <option value="all">{{ __('finance.type_all') }}</option>
                            <option value="outings">{{ __('finance.type_outings') }}</option>
                            <option value="fixed">{{ __('finance.type_fixed') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button wire:click="downloadZip" class="btn btn-primary"><i class="bi bi-file-earmark-zip"></i> {{ __('finance.download_zip') }}</button>
                        <button wire:click="downloadCsv" class="btn btn-outline-primary"><i class="bi bi-filetype-csv"></i> {{ __('finance.export_csv') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between">
                <span class="fw-semibold">{{ __('finance.invoice_archive') }}</span>
                <span>{{ __('finance.total') }}: <strong>{{ number_format($total, 2) }}</strong></span>
            </div>
            <div class="card-body table-responsive">
                <table class="table align-middle">
                    <thead><tr>
                        <th>{{ __('finance.type') }}</th>
                        <th>{{ __('finance.reference') }}</th>
                        <th>{{ __('finance.date') }}</th>
                        <th>{{ __('finance.description') }}</th>
                        <th class="text-end">{{ __('finance.amount') }}</th>
                        <th class="text-end">{{ __('finance.invoice') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse($documents as $doc)
                            <tr wire:key="doc-{{ $doc['type'] }}-{{ $doc['reference'] }}">
                                <td><span class="badge {{ $doc['type'] === 'outings' ? 'bg-info' : 'bg-secondary' }}">{{ __('finance.type_'.$doc['type']) }}</span></td>
                                <td>{{ $doc['reference'] }}</td>
                                <td>{{ optional($doc['date'])->format('d/m/Y') }}</td>
                                <td>{{ $doc['description'] }}</td>
                                <td class="text-end">{{ number_format($doc['amount'], 2) }}</td>
                                <td class="text-end">
                                    @if($doc['path'])
                                        <a href="{{ \Illuminate\Support\Facades\Storage::url($doc['path']) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i></a>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">{{ __('finance.no_documents') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
