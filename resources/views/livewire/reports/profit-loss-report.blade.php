<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                <div class="flex-auto p-2">
                    <form wire:submit="generateReport">
                        <div class="form-row align-items-end">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label>{{ __('report.start-date') }} <span class="text-danger">*</span></label>
                                <input wire:model="start_date" type="date" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="start_date">
                                @error('start_date')
                                <span class="text-danger mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label>{{ __('report.end-date') }} <span class="text-danger">*</span></label>
                                <input wire:model="end_date" type="date" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="end_date">
                                @error('end_date')
                                <span class="text-danger mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-auto mb-3">
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
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

    <div class="row mb-4">
        {{-- Sales --}}
        <div class="col-12 col-lg-4">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                <div class="flex-auto p-2 p-3 d-flex align-items-center">
                    <div class="bg-primary p-3 mfe-3 rounded">
                        <i class="bi bi-receipt font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-primary">{{ format_currency($sales_amount) }}</div>
                        <div class="text-uppercase font-weight-bold small ">{{ $total_sales }} Sales</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Sale Returns --}}
        <div class="col-12 col-lg-4">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                <div class="flex-auto p-2 p-3 d-flex align-items-center">
                    <div class="bg-primary p-3 mfe-3 rounded">
                        <i class="bi bi-arrow-return-left font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-primary">{{ format_currency($sale_returns_amount) }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ $total_sale_returns }} Sale Returns</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Profit --}}
        <div class="col-12 col-lg-4">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                <div class="flex-auto p-2 p-3 d-flex align-items-center">
                    <div class="bg-primary p-3 mfe-3 rounded">
                        <i class="bi bi-trophy font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-primary">{{ format_currency($profit_amount) }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.profit') }}</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Purchases --}}
        <div class="col-12 col-lg-4">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                <div class="flex-auto p-2 p-3 d-flex align-items-center">
                    <div class="bg-primary p-3 mfe-3 rounded">
                        <i class="bi bi-bag font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-primary">{{ format_currency($purchases_amount) }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.purchases') }} {{ $total_purchases }}</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Purchase Returns --}}
        <div class="col-12 col-lg-4">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                <div class="flex-auto p-2 p-3 d-flex align-items-center">
                    <div class="bg-primary p-3 mfe-3 rounded">
                        <i class="bi bi-arrow-return-right font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-primary">{{ format_currency($purchase_returns_amount) }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ $total_purchase_returns }} {{ __('report.purchase-returns') }}</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Expenses --}}
        <div class="col-12 col-lg-4">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                <div class="flex-auto p-2 p-3 d-flex align-items-center">
                    <div class="bg-primary p-3 mfe-3 rounded">
                        <i class="bi bi-wallet2 font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-primary">{{ format_currency($expenses_amount) }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.expenses') }}</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Payments Received --}}
        <div class="col-12 col-lg-4">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                <div class="flex-auto p-2 p-3 d-flex align-items-center">
                    <div class="bg-primary p-3 mfe-3 rounded">
                        <i class="bi bi-cash-stack font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-primary">{{ format_currency($payments_received_amount) }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.payments-received') }}</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Payments Sent --}}
        <div class="col-12 col-lg-4">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                <div class="flex-auto p-2 p-3 d-flex align-items-center">
                    <div class="bg-primary p-3 mfe-3 rounded">
                        <i class="bi bi-cash-stack font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-primary">{{ format_currency($payments_sent_amount) }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.payments-sent') }}</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Payments Net --}}
        <div class="col-12 col-lg-4">
            <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                <div class="flex-auto p-2 p-3 d-flex align-items-center">
                    <div class="bg-primary p-3 mfe-3 rounded">
                        <i class="bi bi-cash-stack font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-primary">{{ format_currency($payments_net_amount) }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.payments-net') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
