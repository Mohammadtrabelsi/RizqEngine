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
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.client_type') }}</span><span>{{ __('customer.'.($customer->client_type ?: 'physical_person')) }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.customer_email') }}</span><span>{{ $customer->customer_email }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.customer_phone') }}</span><span>{{ $customer->customer_phone }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.whatsapp_number') }}</span><span>{{ $customer->whatsapp_number ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.responsible_person') }}</span><span>{{ $customer->responsible_person ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.tax_identification_number') }}</span><span>{{ $customer->tax_identification_number ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.iban') }}</span><span>{{ $customer->iban ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.city') }}</span><span>{{ $customer->city }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.country') }}</span><span>{{ $customer->country }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.address') }}</span><span>{{ $customer->address }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.note') }}</span><span>{{ $customer->note ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('customer.document') }}</span><span>@if ($customer->getFirstMediaUrl('documents'))<a href="{{ $customer->getFirstMediaUrl('documents') }}" target="_blank">{{ $customer->getFirstMedia('documents')->file_name }}</a>@else—@endif</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
