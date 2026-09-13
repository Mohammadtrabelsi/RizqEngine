{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
<div class="container-fluid">
    {{-- Page-level actions --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h5 class="mb-0">{{ __('customer.price_list') }} — {{ $customer->customer_name }}</h5>
        <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> {{ __('customer.back_to_details') }}
        </a>
    </div>

    @include('utils.alerts')

    <div class="row">
        {{-- Add a negotiated price --}}
        <div class="col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-header">{{ __('customer.add_price') }}</div>
                <div class="flex-auto p-3">
                    <form wire:submit="save">
                        <div class="mb-3">
                            <label for="product_id">{{ __('customer.product') }} <span class="text-danger">*</span></label>
                            <select id="product_id" class="form-control" wire:model="product_id">
                                <option value="">{{ __('customer.select_product') }}</option>
                                @foreach ($available as $product)
                                    <option value="{{ $product->id }}">
                                        {{ translatable_string($product->product_name) }} ({{ $product->product_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price">{{ __('customer.price') }} <span class="text-danger">*</span></label>
                            <input id="price" type="number" min="0" step="1" class="form-control" wire:model="price">
                            @error('price') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> {{ __('common.create') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Existing negotiated prices --}}
        <div class="col-lg-8 mb-3">
            <div class="card h-100">
                <div class="card-header">{{ __('customer.negotiated_prices') }}</div>
                <div class="flex-auto p-2">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('customer.product') }}</th>
                                    <th>{{ __('customer.default_price') }}</th>
                                    <th>{{ __('customer.price') }}</th>
                                    <th class="text-end">{{ __('customer.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($prices as $line)
                                    <tr wire:key="price-{{ $line->id }}">
                                        <td>
                                            @if ($line->product)
                                                {{ translatable_string($line->product->product_name) }}
                                                <span class="text-muted small">({{ $line->product->product_code }})</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $line->product?->product_price }}</td>
                                        <td>
                                            @if ($editing_id === $line->id)
                                                <div class="d-flex gap-2">
                                                    <input type="number" min="0" step="1" class="form-control form-control-sm" wire:model="editing_price" style="max-width:120px">
                                                    <button class="btn btn-success btn-sm" wire:click="updatePrice({{ $line->id }})">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                    <button class="btn btn-outline-secondary btn-sm" wire:click="cancelEdit">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </div>
                                                @error('editing_price') <span class="text-danger small">{{ $message }}</span> @enderror
                                            @else
                                                <span class="fw-bold">{{ $line->price }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($editing_id !== $line->id)
                                                <button class="btn btn-sm btn-outline-secondary" wire:click="edit({{ $line->id }})" title="{{ __('customer.edit') }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            @endif
                                            <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $line->id }})"
                                                    wire:confirm="{{ __('customer.confirm_delete_price') }}" title="{{ __('customer.delete') }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">{{ __('customer.no_negotiated_prices') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
