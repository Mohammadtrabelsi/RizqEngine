<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                <div class="flex-auto p-5">
                    <form wire:submit="generateReport">
                        <div class="form-row">
                            <div class="col-lg-3">
                                <div class="mb-4">
                                    <label>{{ __('report.start-date') }} <span class="text-danger">*</span></label>
                                    <input wire:model="start_date" type="date" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                                    @error('start_date') <span class="text-danger mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-4">
                                    <label>{{ __('report.end-date') }} <span class="text-danger">*</span></label>
                                    <input wire:model="end_date" type="date" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                                    @error('end_date') <span class="text-danger mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-4">
                                    <label>{{ __('report.product') }}</label>
                                    <select wire:model="product_id" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                                        <option value="">{{ __('report.all-products') }}</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-4">
                                    <label>{{ __('report.type') }}</label>
                                    <select wire:model="type" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45">
                                        <option value="">{{ __('report.all-types') }}</option>
                                        <option value="in">{{ __('report.stock-in') }}</option>
                                        <option value="out">{{ __('report.stock-out') }}</option>
                                        <option value="adjustment">{{ __('report.adjustment') }}</option>
                                        <option value="opening">{{ __('report.opening') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4 mb-0">
                            <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
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
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                <div class="flex-auto p-5">
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
