<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="flex-auto p-2">
                    <form wire:submit="generateReport">
                        <div class="form-row align-items-end">
                            <div class="col-lg-2 col-md-6 mb-3">
                                <label>{{ __('report.start-date') }} <span class="text-danger">*</span></label>
                                <input wire:model="start_date" type="date" class="form-control">
                                @error('start_date') <span class="text-danger mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-lg-2 col-md-6 mb-3">
                                <label>{{ __('report.end-date') }} <span class="text-danger">*</span></label>
                                <input wire:model="end_date" type="date" class="form-control">
                                @error('end_date') <span class="text-danger mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label>{{ __('report.ranking') }}</label>
                                <select wire:model="direction" class="form-control">
                                    <option value="fast">{{ __('report.fast-moving') }}</option>
                                    <option value="slow">{{ __('report.slow-moving') }}</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label>{{ __('report.show-top') }}</label>
                                <select wire:model="limit" class="form-control">
                                    <option value="10">{{ __('report.top-10') }}</option>
                                    <option value="25">{{ __('report.top-25') }}</option>
                                    <option value="50">{{ __('report.top-50') }}</option>
                                </select>
                            </div>
                            <div class="col-auto mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <span wire:target="generateReport" wire:loading class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <i wire:target="generateReport" wire:loading.remove class="bi bi-shuffle"></i>
                                    {{ __('report.generate-report') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="flex-auto p-2">
                    <h5 class="mb-3">{{ $direction === 'slow' ? __('report.slow-moving-products') : __('report.fast-moving-products') }}</h5>
                    <div class="block w-full overflow-x-auto">
                        <table class="w-full mb-4 text-slate-900 border-collapse [&_tbody_tr:hover]:bg-slate-50 align-middle mb-0">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th scope="col" class="text-center">{{ __('report.ranking') }}</th>
                                    <th scope="col">{{ __('report.product') }}</th>
                                    <th scope="col">{{ __('report.reference') }}</th>
                                    <th scope="col" class="text-end">{{ __('report.units-sold') }}</th>
                                    <th scope="col" class="text-end">{{ __('report.current-stock') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $index => $product)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                        <td>{{ $product->product_name }}</td>
                                        <td class="text-muted">{{ $product->product_code }}</td>
                                        <td class="text-end">{{ $product->units_sold }}</td>
                                        <td class="text-end">{{ $product->product_quantity }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">{{ __('report.no-sales-data') }}</td>
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
