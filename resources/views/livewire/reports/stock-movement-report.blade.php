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
                        <div class="row">
                            @forelse($movements as $movement)
                                <div class="col-xl-4 col-lg-6 mb-4">
                                    <div class="card border h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span>{{ optional($movement->product)->product_name ?? '—' }}</span>
                                            @if($movement->type === 'out')
                                                <span class="badge badge-danger">Out</span>
                                            @elseif($movement->type === 'in')
                                                <span class="badge badge-success">In</span>
                                            @elseif($movement->type === 'opening')
                                                <span class="badge badge-secondary">Opening</span>
                                            @else
                                                <span class="badge badge-info">Adjustment</span>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush mb-0">
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.date') }}</span><span>{{ $movement->created_at->format('d M Y H:i') }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0">
                                                    <span>{{ __('report.change') }}</span>
                                                    <span class="{{ $movement->signed_quantity < 0 ? 'text-danger' : 'text-success' }}">{{ $movement->signed_quantity > 0 ? '+' : '' }}{{ $movement->signed_quantity }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.before') }}</span><span>{{ $movement->quantity_before }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.after') }}</span><span>{{ $movement->quantity_after }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0">
                                                    <span>{{ __('report.reference') }}</span>
                                                    <span class="text-end">
                                                        {{ $movement->reference_type }}{{ $movement->reference_id ? ' #' . $movement->reference_id : '' }}
                                                        @if($movement->note)<div class="text-muted small">{{ $movement->note }}</div>@endif
                                                    </span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.user') }}</span><span>{{ optional($movement->user)->name ?? 'System' }}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center">{{ __('report.no-movements') }}</div>
                            @endforelse
                        </div>
                    </div>
                    {{ $movements->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
