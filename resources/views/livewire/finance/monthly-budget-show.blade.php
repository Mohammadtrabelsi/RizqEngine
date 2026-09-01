{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        {{-- Balance summary --}}
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="text-muted small">{{ __('finance.starting_budget') }}</div>
                    <div class="h4 mb-0">{{ number_format($budget->starting_budget, 2) }}</div>
                </div></div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="text-muted small">{{ __('finance.total_fixed') }}</div>
                    <div class="h4 mb-0">{{ number_format($totalFixed, 2) }}</div>
                </div></div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="text-muted small">{{ __('finance.total_outings') }}</div>
                    <div class="h4 mb-0">{{ number_format($totalOutings, 2) }}</div>
                </div></div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100 {{ $remaining < 0 ? 'border-danger' : '' }}"><div class="card-body">
                    <div class="text-muted small">{{ __('finance.remaining_balance') }}</div>
                    <div class="h4 mb-0 {{ $remaining < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($remaining, 2) }}</div>
                </div></div>
            </div>
        </div>

        <div class="row">
            {{-- Fixed payments --}}
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent fw-semibold">{{ __('finance.fixed_payments') }}</div>
                    <div class="card-body">
                        <div class="table-responsive mb-3">
                            <table class="table table-sm align-middle">
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
                                            <td><span class="badge bg-light text-dark">{{ __('finance.cat_'.$payment->category) }}</span></td>
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
                                    <input type="text" wire:model="label" class="form-control form-control-sm @error('label') is-invalid @enderror" placeholder="{{ __('finance.label') }}">
                                    @error('label') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <select wire:model="category" class="form-select form-select-sm">
                                        @foreach(['rent','utilities','subscription','loan','other'] as $cat)
                                            <option value="{{ $cat }}">{{ __('finance.cat_'.$cat) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" step="0.01" wire:model="amount" class="form-control form-control-sm @error('amount') is-invalid @enderror" placeholder="{{ __('finance.amount') }}">
                                    @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4">
                                    <input type="date" wire:model="due_date" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-4">
                                    <input type="file" wire:model="invoice" class="form-control form-control-sm @error('invoice') is-invalid @enderror">
                                    @error('invoice') <span class="invalid-feedback">{{ $message }}</span> @enderror
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
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">{{ __('finance.outings') }}</span>
                        <a href="{{ route('outings.create') }}" class="btn btn-sm btn-outline-primary">{{ __('finance.add_outing') }} <i class="bi bi-plus"></i></a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
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
                                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($outing->voucher_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-pdf"></i></a>
                                                @endif
                                                <a href="{{ route('outings.edit', $outing) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
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

        <a href="{{ route('monthly-budgets.index') }}" class="btn btn-outline-secondary">{{ __('app.back') }}</a>
    </div>
</div>
