{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        {{-- Balance summary --}}
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="relative flex flex-col min-w-0 break-words border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm h-100"><div class="flex-auto p-2">
                    <div class="text-muted small">{{ __('finance.starting_budget') }}</div>
                    <div class="h4 mb-0">{{ number_format($budget->starting_budget, 2) }}</div>
                </div></div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="relative flex flex-col min-w-0 break-words border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm h-100"><div class="flex-auto p-2">
                    <div class="text-muted small">{{ __('finance.total_fixed') }}</div>
                    <div class="h4 mb-0">{{ number_format($totalFixed, 2) }}</div>
                </div></div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="relative flex flex-col min-w-0 break-words border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm h-100"><div class="flex-auto p-2">
                    <div class="text-muted small">{{ __('finance.total_outings') }}</div>
                    <div class="h4 mb-0">{{ number_format($totalOutings, 2) }}</div>
                </div></div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="relative flex flex-col min-w-0 break-words border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm h-100 {{ $remaining < 0 ? 'border-danger' : '' }}"><div class="flex-auto p-2">
                    <div class="text-muted small">{{ __('finance.remaining_balance') }}</div>
                    <div class="h4 mb-0 {{ $remaining < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($remaining, 2) }}</div>
                </div></div>
            </div>
        </div>

        <div class="row">
            {{-- Fixed payments --}}
            <div class="col-lg-6 mb-4">
                <div class="relative flex flex-col min-w-0 break-words border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm h-100">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl bg-transparent fw-semibold">{{ __('finance.fixed_payments') }}</div>
                    <div class="flex-auto p-2">
                        <div class="block w-full overflow-x-auto mb-3">
                            <table class="w-full mb-4 text-slate-900 border-collapse table-sm align-middle">
                                <thead><tr>
                                    <th>{{ __('finance.label') }}</th>
                                    <th>{{ __('finance.category') }}</th>
                                    <th class="text-end">{{ __('finance.amount') }}</th>
                                    <th></th>
                                </tr></thead>
                                <tbody>
                                    @forelse($fixedPayments as $payment)
                                        <tr wire:key="fp-{{ $payment->id }}">
                                            <td>{{ $payment->label }}
                                                @if($payment->hasInvoice)
                                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($payment->invoice_path) }}" target="_blank" title="{{ __('finance.invoice') }}"><i class="bi bi-paperclip"></i></a>
                                                @endif
                                            </td>
                                            <td><span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-light text-dark">{{ __('finance.cat_'.$payment->category) }}</span></td>
                                            <td class="text-end">{{ number_format($payment->amount, 2) }}</td>
                                            <td class="text-end"><button class="btn btn-sm btn-outline-danger" wire:click="deleteFixedPayment({{ $payment->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted">{{ __('finance.no_payments') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Inline add --}}
                        <form wire:submit="addFixedPayment" class="border-top pt-3">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="text" wire:model="label" class="form-control form-control-sm @error('label') !border-red-500 @enderror" placeholder="{{ __('finance.label') }}">
                                    @error('label') <span class="block w-full mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <select wire:model="category" class="form-select !py-1 !text-xs">
                                        @foreach(['rent','utilities','subscription','loan','other'] as $cat)
                                            <option value="{{ $cat }}">{{ __('finance.cat_'.$cat) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" step="0.01" wire:model="amount" class="form-control form-control-sm @error('amount') !border-red-500 @enderror" placeholder="{{ __('finance.amount') }}">
                                    @error('amount') <span class="block w-full mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4">
                                    <input type="date" wire:model="due_date" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-4">
                                    <input type="file" wire:model="invoice" class="form-control form-control-sm @error('invoice') !border-red-500 @enderror">
                                    @error('invoice') <span class="block w-full mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm mt-2">{{ __('finance.add_payment') }} <i class="bi bi-plus"></i></button>
                            <span wire:loading wire:target="invoice" class="text-muted small ms-2">{{ __('finance.uploading') }}…</span>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Outings in the month --}}
            <div class="col-lg-6 mb-4">
                <div class="relative flex flex-col min-w-0 break-words border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm h-100">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl bg-transparent d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">{{ __('finance.outings') }}</span>
                        <a href="{{ route('outings.create') }}" class="btn btn-sm btn-outline">{{ __('finance.add_outing') }} <i class="bi bi-plus"></i></a>
                    </div>
                    <div class="flex-auto p-2">
                        <div class="block w-full overflow-x-auto">
                            <table class="w-full mb-4 text-slate-900 border-collapse table-sm align-middle">
                                <thead><tr>
                                    <th>{{ __('finance.reference') }}</th>
                                    <th>{{ __('finance.date') }}</th>
                                    <th>{{ __('finance.location') }}</th>
                                    <th class="text-end">{{ __('finance.total') }}</th>
                                    <th></th>
                                </tr></thead>
                                <tbody>
                                    @forelse($outings as $outing)
                                        <tr wire:key="out-{{ $outing->id }}">
                                            <td>{{ $outing->reference }}</td>
                                            <td>{{ $outing->date->format('d/m') }}</td>
                                            <td>{{ $outing->location }}</td>
                                            <td class="text-end">{{ number_format($outing->total(), 2) }}</td>
                                            <td class="text-end">
                                                @if($outing->hasVoucher())
                                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($outing->voucher_path) }}" target="_blank" class="btn btn-sm btn-secondary"><i class="bi bi-file-earmark-pdf"></i></a>
                                                @endif
                                                <a href="{{ route('outings.edit', $outing) }}" class="btn btn-sm btn-outline"><i class="bi bi-pencil"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted">{{ __('finance.no_outings') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ route('monthly-budgets.index') }}" class="btn btn-secondary">{{ __('app.back') }}</a>
    </div>
</div>
