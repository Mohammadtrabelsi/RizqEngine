{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        {{-- Filters --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="flex-auto p-2">
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
                        <button wire:click="downloadCsv" class="btn btn-outline"><i class="bi bi-filetype-csv"></i> {{ __('finance.export_csv') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">{{ __('finance.invoice_archive') }}</h5>
            <span>{{ __('finance.total') }}: <strong>{{ number_format($total, 2) }}</strong></span>
        </div>

        <div class="row">
            @forelse($documents as $doc)
                <div class="col-xl-4 col-lg-6 mb-4" wire:key="doc-{{ $doc['type'] }}-{{ $doc['reference'] }}">
                    <div class="card h-100">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
                            <span class="fw-bold">{{ $doc['reference'] }}</span>
                            <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline {{ $doc['type'] === 'outings' ? 'bg-info' : 'bg-secondary' }}">{{ __('finance.type_'.$doc['type']) }}</span>
                        </div>
                        <div class="flex-auto p-2">
                            <ul class="list-group list-group-flush mb-0">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('finance.date') }}</span><span>{{ optional($doc['date'])->format('d/m/Y') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('finance.description') }}</span><span>{{ $doc['description'] }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0 fw-semibold"><span>{{ __('finance.amount') }}</span><span>{{ number_format($doc['amount'], 2) }}</span></li>
                            </ul>
                        </div>
                        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 text-center">
                            @if($doc['path'])
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($doc['path']) }}" target="_blank" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-download me-1"></i> {{ __('finance.invoice') }}
                                </a>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('finance.no_documents') }}</div></div>
                </div>
            @endforelse
        </div>
    </div>
</div>
