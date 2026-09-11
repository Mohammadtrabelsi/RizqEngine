{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                @can('create_withholding_taxes')
                <a href="{{ route('withholding-taxes.create') }}" class="btn btn-primary">
                    {{ __('withholding.add_withholding') }} <i class="bi bi-plus"></i>
                </a>
                @endcan
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
            </div>
        </div>

        <div class="d-flex justify-content-center mb-3">{{ $withholdingTaxes->links('pagination::bootstrap-5') }}</div>
        <div class="row">
            @forelse($withholdingTaxes as $withholding)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="wht-{{ $withholding->id }}">
                    <div class="card h-100">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $withholding->name }}</h5>
                            @unless($withholding->active)
                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-secondary">{{ __('withholding.inactive') }}</span>
                            @endunless
                        </div>
                        <div class="flex-auto p-2">
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('withholding.code') }}</span><span>{{ $withholding->code }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('withholding.rate') }}</span><span>{{ rtrim(rtrim(number_format($withholding->rate, 3), '0'), '.') }}%</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('withholding.calculation_base') }}</span><span>{{ __($withholding->calculation_base->label()) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('withholding.scope') }}</span><span>
                                    @if($withholding->applicable_to_purchases) {{ __('withholding.purchases') }} @endif
                                    @if($withholding->applicable_to_sales) {{ __('withholding.sales') }} @endif
                                </span></li>
                            </ul>
                            <div class="btn-group">
                                @can('edit_withholding_taxes')
                                <a href="{{ route('withholding-taxes.edit', $withholding) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>
                                @endcan
                                @can('delete_withholding_taxes')
                                <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $withholding->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('withholding.no_withholding_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $withholdingTaxes->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
