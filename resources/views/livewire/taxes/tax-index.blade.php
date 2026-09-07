{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <a href="{{ route('taxes.create') }}" class="btn btn-primary">
                    {{ __('taxes.add_tax') }} <i class="bi bi-plus"></i>
                </a>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
            </div>
        </div>

        <div class="d-flex justify-content-center mb-3">{{ $taxes->links('pagination::bootstrap-5') }}</div>
        <div class="row">
            @forelse($taxes as $tax)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="tax-{{ $tax->id }}">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $tax->name }}</h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ $tax->type === 'fixed' ? __('taxes.amount') : __('taxes.rate') }}</span>
                                    <span>{{ $tax->type === 'fixed' ? format_currency($tax->rate) : rtrim(rtrim(number_format($tax->rate, 2), '0'), '.').'%' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('taxes.apply_to') }}</span><span>{{ $tax->apply_to === 'product' ? __('taxes.apply_to_product') : __('taxes.apply_to_order') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('taxes.order') }}</span><span>{{ $tax->order }}</span></li>
                            </ul>
                            <div class="btn-group">
                                <a href="{{ route('taxes.edit', $tax) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $tax->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">{{ __('taxes.no_taxes_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $taxes->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
