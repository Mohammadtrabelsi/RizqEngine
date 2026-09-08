<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <a href="{{ route('adjustments.create') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                {{ __('adjustment.add_adjustment') }} <i class="bi bi-plus"></i>
            </a>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $adjustments->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($adjustments as $adjustment)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="adjustment-{{ $adjustment->id }}">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 h-100">
                    <div class="flex-auto p-2">
                        <h5 class="mb-3 text-lg font-semibold text-slate-900">{{ $adjustment->reference }}</h5>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('adjustment.date') }}</span><span>{{ $adjustment->date }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('adjustment.products') }}</span><span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-info">{{ $adjustment->adjusted_products_count }}</span></li>
                        </ul>
                        <div class="btn-group">
                            @can('edit_adjustments')
                                <a href="{{ route('adjustments.edit', $adjustment->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-indigo-600 border-indigo-600 hover:bg-indigo-600 hover:!text-white !px-3 !py-1.5 !text-xs"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('show_adjustments')
                                <a href="{{ route('adjustments.show', $adjustment->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-indigo-600 border-indigo-600 hover:bg-indigo-600 hover:!text-white !px-3 !py-1.5 !text-xs"><i class="bi bi-eye"></i></a>
                            @endcan
                            @can('delete_adjustments')
                                <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-red-500 border-red-500 hover:bg-red-500 hover:!text-white !px-3 !py-1.5 !text-xs" wire:click="delete({{ $adjustment->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900"><div class="flex-auto p-2 text-center text-muted">{{ __('adjustment.no_adjustments_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $adjustments->links('pagination::bootstrap-5') }}
    </div>
</div>
