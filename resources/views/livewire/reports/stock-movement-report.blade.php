<div>
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form wire:submit="generateReport">
                        <div class="form-row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>{{ __('report.start-date') }} <span class="text-danger">*</span></label>
                                    <input wire:model="start_date" type="date" class="form-control">
                                    @error('start_date') <span class="text-danger mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>{{ __('report.end-date') }} <span class="text-danger">*</span></label>
                                    <input wire:model="end_date" type="date" class="form-control">
                                    @error('end_date') <span class="text-danger mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>{{ __('report.product') }}</label>
                                    <select wire:model="product_id" class="form-control">
                                        <option value="">{{ __('report.all-products') }}</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>{{ __('report.type') }}</label>
                                    <select wire:model="type" class="form-control">
                                        <option value="">{{ __('report.all-types') }}</option>
                                        <option value="in">{{ __('report.stock-in') }}</option>
                                        <option value="out">{{ __('report.stock-out') }}</option>
                                        <option value="adjustment">{{ __('report.adjustment') }}</option>
                                        <option value="opening">{{ __('report.opening') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <span wire:target="generateReport" wire:loading class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <i wire:target="generateReport" wire:loading.remove class="bi bi-shuffle"></i>
                                {{ __('report.filter-report') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th scope="col">{{ __('report.product') }}</th>
                                    <th scope="col">{{ __('report.type') }}</th>
                                    <th scope="col">{{ __('report.date') }}</th>
                                    <th scope="col" class="text-end">{{ __('report.change') }}</th>
                                    <th scope="col" class="text-end">{{ __('report.before') }}</th>
                                    <th scope="col" class="text-end">{{ __('report.after') }}</th>
                                    <th scope="col">{{ __('report.reference') }}</th>
                                    <th scope="col">{{ __('report.user') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movements as $movement)
                                    <tr>
                                        <td>{{ optional($movement->product)->product_name ?? '—' }}</td>
                                        <td>
                                            @if($movement->type === 'out')
                                                <span class="badge badge-danger">Out</span>
                                            @elseif($movement->type === 'in')
                                                <span class="badge badge-success">In</span>
                                            @elseif($movement->type === 'opening')
                                                <span class="badge badge-secondary">Opening</span>
                                            @else
                                                <span class="badge badge-info">Adjustment</span>
                                            @endif
                                        </td>
                                        <td>{{ $movement->created_at->format('d M Y H:i') }}</td>
                                        <td class="text-end {{ $movement->signed_quantity < 0 ? 'text-danger' : 'text-success' }}">{{ $movement->signed_quantity > 0 ? '+' : '' }}{{ $movement->signed_quantity }}</td>
                                        <td class="text-end">{{ $movement->quantity_before }}</td>
                                        <td class="text-end">{{ $movement->quantity_after }}</td>
                                        <td>
                                            {{ $movement->reference_type }}{{ $movement->reference_id ? ' #' . $movement->reference_id : '' }}
                                            @if($movement->note)<div class="text-muted small">{{ $movement->note }}</div>@endif
                                        </td>
                                        <td>{{ optional($movement->user)->name ?? 'System' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">{{ __('report.no-movements') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div @class(['mt-3' => $movements->hasPages()])>{{ $movements->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
