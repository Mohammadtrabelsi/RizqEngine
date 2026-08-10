<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            @can('create_customers')
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    {{ __('customer.add_customer') }} <i class="bi bi-plus"></i>
                </a>
            @endcan
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="row">
        @forelse($customers as $customer)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="customer-{{ $customer->id }}">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $customer->customer_name }}</h5>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item px-0"><i class="bi bi-envelope"></i> {{ $customer->customer_email }}</li>
                            <li class="list-group-item px-0"><i class="bi bi-telephone"></i> {{ $customer->customer_phone }}</li>
                            @if ($customer->tax_identification_number)
                                <li class="list-group-item px-0"><i class="bi bi-receipt"></i> {{ $customer->tax_identification_number }}</li>
                            @endif
                        </ul>
                        <div class="btn-group">
                            @can('edit_customers')
                                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-info btn-sm"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('show_customers')
                                <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i></a>
                            @endcan
                            @can('delete_customers')
                                <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $customer->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-muted">{{ __('customer.no_customers_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $customers->links() }}
    </div>
</div>
