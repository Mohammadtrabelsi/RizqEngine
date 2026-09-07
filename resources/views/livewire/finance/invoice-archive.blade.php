{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        {{-- Filters --}}
        <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm mb-4">
            <div class="flex-auto p-5">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="inline-block mb-1.5 text-sm font-medium text-slate-900">{{ __('finance.start_date') }}</label>
                        <input type="date" wire:model.live="start_date" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                    </div>
                    <div class="col-md-3">
                        <label class="inline-block mb-1.5 text-sm font-medium text-slate-900">{{ __('finance.end_date') }}</label>
                        <input type="date" wire:model.live="end_date" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                    </div>
                    <div class="col-md-3">
                        <label class="inline-block mb-1.5 text-sm font-medium text-slate-900">{{ __('finance.type') }}</label>
                        <select wire:model.live="type" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 pr-9 text-sm leading-normal text-slate-900 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                            <option value="all">{{ __('finance.type_all') }}</option>
                            <option value="outings">{{ __('finance.type_outings') }}</option>
                            <option value="fixed">{{ __('finance.type_fixed') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button wire:click="downloadZip" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700"><i class="bi bi-file-earmark-zip"></i> {{ __('finance.download_zip') }}</button>
                        <button wire:click="downloadCsv" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-indigo-600 border-indigo-600 hover:bg-indigo-600 hover:!text-white"><i class="bi bi-filetype-csv"></i> {{ __('finance.export_csv') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl bg-transparent d-flex justify-content-between">
                <span class="fw-semibold">{{ __('finance.invoice_archive') }}</span>
                <span>{{ __('finance.total') }}: <strong>{{ number_format($total, 2) }}</strong></span>
            </div>
            <div class="flex-auto p-5 block w-full overflow-x-auto">
                <table class="w-full mb-4 text-slate-900 border-collapse align-middle">
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
                                <td><span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline {{ $doc['type'] === 'outings' ? 'bg-info' : 'bg-secondary' }}">{{ __('finance.type_'.$doc['type']) }}</span></td>
                                <td>{{ $doc['reference'] }}</td>
                                <td>{{ optional($doc['date'])->format('d/m/Y') }}</td>
                                <td>{{ $doc['description'] }}</td>
                                <td class="text-end">{{ number_format($doc['amount'], 2) }}</td>
                                <td class="text-end">
                                    @if($doc['path'])
                                        <a href="{{ \Illuminate\Support\Facades\Storage::url($doc['path']) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !px-3 !py-1.5 !text-xs !text-slate-600 border-slate-300 hover:bg-slate-50 hover:!text-slate-900 hover:border-slate-400"><i class="bi bi-download"></i></a>
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
