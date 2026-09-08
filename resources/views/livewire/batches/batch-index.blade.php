{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                @can('create_batches')
                    <a href="{{ route('batches.create') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                        {{ __('batches.add_batch') }} <i class="bi bi-plus"></i>
                    </a>
                @endcan
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="{{ __('app.search') }}">
            </div>
        </div>

        <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
            <div class="flex-auto p-2 block w-full overflow-x-auto">
                <table class="w-full mb-4 text-slate-900 border-collapse [&_tbody_tr:hover]:bg-slate-50 align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('batches.batch_number') }}</th>
                            <th>{{ __('batches.product') }}</th>
                            <th class="text-end">{{ __('batches.quantity') }}</th>
                            <th>{{ __('batches.manufactured_date') }}</th>
                            <th>{{ __('batches.expiry_date') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $batch)
                            <tr wire:key="batch-{{ $batch->id }}" class="{{ $batch->is_expired ? 'table-danger' : '' }}">
                                <td>{{ $batch->batch_number }}</td>
                                <td>{{ $batch->product?->product_name }} <small class="text-muted">{{ $batch->product?->product_code }}</small></td>
                                <td class="text-end">{{ $batch->quantity }}</td>
                                <td>{{ $batch->manufactured_date?->format('Y-m-d') ?: '—' }}</td>
                                <td>
                                    {{ $batch->expiry_date?->format('Y-m-d') ?: '—' }}
                                    @if($batch->is_expired)
                                        <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-danger">{{ __('batches.expired') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        @can('edit_batches')
                                            <a href="{{ route('batches.edit', $batch) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700 !px-3 !py-1.5 !text-xs"><i class="bi bi-pencil"></i></a>
                                        @endcan
                                        @can('delete_batches')
                                            <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-red-500 !text-white border-red-500 hover:bg-red-600 hover:border-red-600 !px-3 !py-1.5 !text-xs" wire:click="delete({{ $batch->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">{{ __('batches.no_batches_found') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-3">{{ $batches->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
