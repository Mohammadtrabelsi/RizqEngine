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
                                    <label>{{ __('report.ranking') }}</label>
                                    <select wire:model="direction" class="form-control">
                                        <option value="fast">{{ __('report.fast-moving') }}</option>
                                        <option value="slow">{{ __('report.slow-moving') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>{{ __('report.show-top') }}</label>
                                    <select wire:model="limit" class="form-control">
                                        <option value="10">{{ __('report.top-10') }}</option>
                                        <option value="25">{{ __('report.top-25') }}</option>
                                        <option value="50">{{ __('report.top-50') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <span wire:target="generateReport" wire:loading class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <i wire:target="generateReport" wire:loading.remove class="bi bi-shuffle"></i>
                                {{ __('report.generate-report') }}
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
                    <h5 class="mb-3">{{ $direction === 'slow' ? __('report.slow-moving-products') : __('report.fast-moving-products') }}</h5>
                    <div class="table-responsive">
                        <div class="row">
                            @forelse($products as $index => $product)
                                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <h6 class="mb-1">{{ $index + 1 }}. {{ $product->product_name }}</h6>
                                            <p class="text-muted small mb-2">{{ $product->product_code }}</p>
                                            <ul class="list-group list-group-flush mb-0">
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.units-sold') }}</span><span>{{ $product->units_sold }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('report.current-stock') }}</span><span>{{ $product->product_quantity }}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center">{{ __('report.no-sales-data') }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
