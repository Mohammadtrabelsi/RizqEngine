{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @if ($customer->getFirstMediaUrl('images'))
                        <div class="text-center mb-4">
                            <img src="{{ $customer->getFirstMediaUrl('images') }}" class="img-max-180 img-fluid img-thumbnail rounded-circle" alt="Customer Image">
                        </div>
                    @endif
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.customer_name') }}</span><span>{{ $customer->customer_name }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.customer_email') }}</span><span>{{ $customer->customer_email }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.customer_phone') }}</span><span>{{ $customer->customer_phone }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.tax_identification_number') }}</span><span>{{ $customer->tax_identification_number ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.city') }}</span><span>{{ $customer->city }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.country') }}</span><span>{{ $customer->country }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.address') }}</span><span>{{ $customer->address }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
