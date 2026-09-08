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
                                    <label>{{ __('report.product') }}</label>
                                    <select wire:model="product_id" class="form-control">
                                        <option value="">{{ __('report.all-products') }}</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                    <label>{{ __('report.type') }}</label>
                                    <select wire:model="type" class="form-control">
                                        <option value="">{{ __('report.all-types') }}</option>
                                        <option value="in">{{ __('report.stock-in') }}</option>
                                        <option value="out">{{ __('report.stock-out') }}</option>
                                        <option value="adjustment">{{ __('report.adjustment') }}</option>
                                        <option value="opening">{{ __('report.opening') }}</option>
                                    </select>
                            </div>
                            <div class="col-auto mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <span wire:target="generateReport" wire:loading class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <i wire:target="generateReport" wire:loading.remove class="bi bi-shuffle"></i>
                                    {{ __('report.filter-report') }}
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
                    <div class="block w-full overflow-x-auto">
                        <table class="w-full mb-4 text-slate-900 border-collapse [&_tbody_tr:hover]:bg-slate-50 align-middle mb-0">
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
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-red-100 text-red-700">Out</span>
                                            @elseif($movement->type === 'in')
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">In</span>
                                            @elseif($movement->type === 'opening')
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-slate-100 text-slate-600">Opening</span>
                                            @else
                                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-cyan-100 text-cyan-700">Adjustment</span>
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
