{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <a href="{{ route('outings.create') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">{{ __('finance.add_outing') }} <i class="bi bi-plus"></i></a>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="{{ __('app.search') }}">
            </div>
        </div>

        <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
            <div class="flex-auto p-5 block w-full overflow-x-auto">
                <table class="w-full mb-4 text-slate-900 border-collapse align-middle">
                    <thead><tr>
                        <th>{{ __('finance.reference') }}</th>
                        <th>{{ __('finance.date') }}</th>
                        <th>{{ __('finance.location') }}</th>
                        <th>{{ __('finance.purpose') }}</th>
                        <th class="text-end">{{ __('finance.total') }}</th>
                        <th class="text-end">{{ __('finance.actions') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse($outings as $outing)
                            <tr wire:key="outing-{{ $outing->id }}">
                                <td>{{ $outing->reference }}</td>
                                <td>{{ $outing->date->format('d/m/Y') }}</td>
                                <td>{{ $outing->location }}</td>
                                <td>{{ $outing->purpose }}</td>
                                <td class="text-end">{{ number_format($outing->total(), 2) }}</td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        @if($outing->hasVoucher())
                                            <a href="{{ \Illuminate\Support\Facades\Storage::url($outing->voucher_path) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-slate-600 border-slate-300 hover:bg-slate-50 hover:!text-slate-900 hover:border-slate-400 !px-3 !py-1.5 !text-xs" title="{{ __('finance.voucher') }}"><i class="bi bi-file-earmark-pdf"></i></a>
                                        @endif
                                        <a href="{{ route('outings.edit', $outing) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700 !px-3 !py-1.5 !text-xs"><i class="bi bi-pencil"></i></a>
                                        <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-red-500 !text-white border-red-500 hover:bg-red-600 hover:border-red-600 !px-3 !py-1.5 !text-xs" wire:click="delete({{ $outing->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">{{ __('finance.no_outings') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-3">{{ $outings->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
