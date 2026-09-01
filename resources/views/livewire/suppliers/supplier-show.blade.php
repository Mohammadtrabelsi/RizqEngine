{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @if ($supplier->getFirstMediaUrl('images'))
                        <div class="text-center mb-4">
                            <img src="{{ $supplier->getFirstMediaUrl('images') }}" class="img-max-180 img-fluid img-thumbnail rounded-circle" alt="Supplier Image">
                        </div>
                    @endif
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.supplier_name') }}</span><span>{{ $supplier->supplier_name }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.supplier_email') }}</span><span>{{ $supplier->supplier_email }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.supplier_phone') }}</span><span>{{ $supplier->supplier_phone }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.tax_identification_number') }}</span><span>{{ $supplier->tax_identification_number ?: '—' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.city') }}</span><span>{{ $supplier->city }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.country') }}</span><span>{{ $supplier->country }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('supplier.address') }}</span><span>{{ $supplier->address }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
