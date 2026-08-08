<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            @can('create_currencies')
                <a href="{{ route('currencies.create') }}" class="btn btn-primary">
                    {{ __('currency.add_currency') }} <i class="bi bi-plus"></i>
                </a>
            @endcan
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="row">
        @forelse($currencies as $currency)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="currency-{{ $currency->id }}">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $currency->currency_name }} <small class="text-muted">({{ $currency->code }})</small></h5>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('currency.symbol') }}</span><span>{{ $currency->symbol }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('currency.thousand_separator') }}</span><span>{{ $currency->thousand_separator }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('currency.decimal_separator') }}</span><span>{{ $currency->decimal_separator }}</span></li>
                        </ul>
                        <div class="btn-group">
                            @can('edit_currencies')
                                <a href="{{ route('currencies.edit', $currency->id) }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endcan
                            @can('delete_currencies')
                                <button type="button" class="btn btn-danger btn-sm"
                                    wire:click="delete({{ $currency->id }})"
                                    wire:confirm="{{ __('app.are_you_sure') }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-muted">{{ __('currency.no_currencies_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $currencies->links() }}
    </div>
</div>
