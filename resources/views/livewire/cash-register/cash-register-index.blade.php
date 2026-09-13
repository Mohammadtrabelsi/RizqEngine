{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-4">
                @if($current)
                    <div class="card border-0 shadow-sm">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-cash-stack text-primary"></i> {{ __('cash_register.current_session') }}</h5>
                            <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-success">{{ __('cash_register.open') }}</span>
                        </div>
                        <div class="flex-auto p-2">
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">{{ __('cash_register.opened_at') }}</span><span>{{ $current->opened_at->format('Y-m-d H:i') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">{{ __('cash_register.opening_float') }}</span><span>{{ number_format($current->opening_float / 100, 2) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">{{ __('cash_register.cash_sales') }}</span><span>{{ number_format($cashSales / 100, 2) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0 fw-bold"><span class="text-muted fw-normal">{{ __('cash_register.expected_cash') }}</span><span>{{ number_format($expected / 100, 2) }}</span></li>
                            </ul>
                            @can('close_cash_register')
                            <form wire:submit="close">
                                <div class="mb-4">
                                    <label>{{ __('cash_register.counted_amount') }}</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('counted_amount') !border-red-500 @enderror" wire:model="counted_amount">
                                    @error('counted_amount') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-4">
                                    <label>{{ __('cash_register.note') }}</label>
                                    <textarea class="form-control" rows="2" wire:model="note"></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger w-100">{{ __('cash_register.close_session') }} (Z)</button>
                            </form>
                            @endcan
                        </div>
                    </div>
                @else
                    <div class="card border-0 shadow-sm">
                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                            <h5 class="mb-0"><i class="bi bi-unlock text-primary"></i> {{ __('cash_register.open_session') }}</h5>
                        </div>
                        <div class="flex-auto p-2">
                            @can('open_cash_register')
                            <form wire:submit="open">
                                <div class="mb-4">
                                    <label>{{ __('cash_register.opening_float') }}</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('opening_float') !border-red-500 @enderror" wire:model="opening_float">
                                    @error('opening_float') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-4">
                                    <label>{{ __('cash_register.note') }}</label>
                                    <textarea class="form-control" rows="2" wire:model="note"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">{{ __('cash_register.open_session') }}</button>
                            </form>
                            @else
                                <p class="text-muted mb-0">{{ __('cash_register.no_open_session') }}</p>
                            @endcan
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                        <h5 class="mb-0"><i class="bi bi-journal-text text-primary"></i> {{ __('cash_register.operations') }}</h5>
                    </div>
                    <div class="flex-auto p-2">
                        {{-- Filters --}}
                        <div class="form-row align-items-end mb-3">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label>{{ __('cash_register.start_date') }}</label>
                                <input type="date" class="form-control" wire:model.live="start_date" max="{{ $end_date }}">
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label>{{ __('cash_register.end_date') }}</label>
                                <input type="date" class="form-control" wire:model.live="end_date" min="{{ $start_date }}">
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label>{{ __('cash_register.operation_type') }}</label>
                                <select class="form-control" wire:model.live="operation_type">
                                    <option value="all">{{ __('cash_register.all_operations') }}</option>
                                    <option value="sale">{{ __('cash_register.selling') }}</option>
                                    <option value="commande">{{ __('cash_register.ordering') }}</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="d-block">&nbsp;</label>
                                <button type="button" class="btn btn-outline-secondary" wire:click="resetFilters">
                                    <i class="bi bi-arrow-counterclockwise"></i> {{ __('cash_register.reset') }}
                                </button>
                            </div>
                        </div>

                        {{-- Summary --}}
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="flex-auto p-2">
                                        <span class="text-muted d-block">{{ __('cash_register.total_selling') }}</span>
                                        <span class="h5 mb-0 text-success">{{ number_format($totals['sale']['total'] / 100, 2) }}</span>
                                        <small class="text-muted d-block">{{ __('cash_register.operations_count', ['count' => $totals['sale']['count']]) }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="flex-auto p-2">
                                        <span class="text-muted d-block">{{ __('cash_register.total_ordering') }}</span>
                                        <span class="h5 mb-0 text-primary">{{ number_format($totals['commande']['total'] / 100, 2) }}</span>
                                        <small class="text-muted d-block">{{ __('cash_register.operations_count', ['count' => $totals['commande']['count']]) }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="flex-auto p-2">
                                        <span class="text-muted d-block">{{ __('cash_register.grand_total') }}</span>
                                        <span class="h5 mb-0">{{ number_format($totals['total'] / 100, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Ledger --}}
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('cash_register.date') }}</th>
                                        <th>{{ __('cash_register.type') }}</th>
                                        <th>{{ __('cash_register.reference') }}</th>
                                        <th>{{ __('cash_register.customer') }}</th>
                                        <th class="text-end">{{ __('cash_register.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $operation)
                                        <tr wire:key="op-{{ $operation->type }}-{{ $operation->id }}">
                                            <td>{{ \Illuminate\Support\Carbon::parse($operation->date)->format('Y-m-d') }}</td>
                                            <td>
                                                @if($operation->type === 'sale')
                                                    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-success">{{ __('cash_register.selling') }}</span>
                                                @else
                                                    <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-primary">{{ __('cash_register.ordering') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $operation->reference ?: '—' }}</td>
                                            <td>{{ $operation->customer_name }}</td>
                                            <td class="text-end">{{ number_format($operation->total_amount / 100, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">{{ __('cash_register.no_transactions') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">{{ $transactions->links('pagination::bootstrap-5') }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                        <h5 class="mb-0"><i class="bi bi-clock-history text-primary"></i> {{ __('cash_register.history') }}</h5>
                    </div>
                    <div class="flex-auto p-2">
                        <div class="row">
                            @forelse($sessions as $session)
                                <div class="col-xl-4 col-lg-6 mb-4" wire:key="session-{{ $session->id }}">
                                    <div class="card h-100">
                                        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">{{ $session->user?->name }}</span>
                                            <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-{{ $session->status->color() }}">{{ $session->status->label() }}</span>
                                        </div>
                                        <div class="flex-auto p-2">
                                            <ul class="list-group list-group-flush mb-0">
                                                <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">{{ __('cash_register.opened_at') }}</span><span>{{ $session->opened_at->format('Y-m-d H:i') }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">{{ __('cash_register.opening_float') }}</span><span>{{ number_format($session->opening_float / 100, 2) }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">{{ __('cash_register.expected_cash') }}</span><span>{{ $session->expected_amount !== null ? number_format($session->expected_amount / 100, 2) : '—' }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">{{ __('cash_register.counted_amount') }}</span><span>{{ $session->closing_amount !== null ? number_format($session->closing_amount / 100, 2) : '—' }}</span></li>
                                                <li class="list-group-item d-flex justify-content-between px-0 fw-bold">
                                                    <span class="text-muted fw-normal">{{ __('cash_register.difference') }}</span>
                                                    <span class="{{ ($session->difference ?? 0) < 0 ? 'text-danger' : (($session->difference ?? 0) > 0 ? 'text-warning' : '') }}">
                                                        {{ $session->difference !== null ? number_format($session->difference / 100, 2) : '—' }}
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-center text-muted mb-0">{{ __('cash_register.no_sessions') }}</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="d-flex justify-content-center">{{ $sessions->links('pagination::bootstrap-5') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
