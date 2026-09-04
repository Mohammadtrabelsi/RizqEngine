{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                @can('create_batches')
                    <a href="{{ route('batches.create') }}" class="btn btn-primary">
                        {{ __('batches.add_batch') }} <i class="bi bi-plus"></i>
                    </a>
                @endcan
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
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
                                        <span class="badge bg-danger">{{ __('batches.expired') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        @can('edit_batches')
                                            <a href="{{ route('batches.edit', $batch) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>
                                        @endcan
                                        @can('delete_batches')
                                            <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $batch->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
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
