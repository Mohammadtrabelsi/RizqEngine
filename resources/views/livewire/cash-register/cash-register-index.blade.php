{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-5 mb-4">
                @if($current)
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title d-flex justify-content-between align-items-center">
                                {{ __('cash_register.current_session') }}
                                <span class="badge bg-success">{{ __('cash_register.open') }}</span>
                            </h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('cash_register.opened_at') }}</span><span>{{ $current->opened_at->format('Y-m-d H:i') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('cash_register.opening_float') }}</span><span>{{ number_format($current->opening_float / 100, 2) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('cash_register.cash_sales') }}</span><span>{{ number_format($cashSales / 100, 2) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0 fw-bold"><span>{{ __('cash_register.expected_cash') }}</span><span>{{ number_format($expected / 100, 2) }}</span></li>
                            </ul>
                            @can('close_cash_register')
                            <form wire:submit="close">
                                <div class="form-group">
                                    <label>{{ __('cash_register.counted_amount') }}</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('counted_amount') is-invalid @enderror" wire:model="counted_amount">
                                    @error('counted_amount') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
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
                        <div class="card-body">
                            <h5 class="card-title">{{ __('cash_register.open_session') }}</h5>
                            @can('open_cash_register')
                            <form wire:submit="open">
                                <div class="form-group">
                                    <label>{{ __('cash_register.opening_float') }}</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('opening_float') is-invalid @enderror" wire:model="opening_float">
                                    @error('opening_float') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
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

            <div class="col-lg-7 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body table-responsive">
                        <h5 class="card-title">{{ __('cash_register.history') }}</h5>
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('cash_register.cashier') }}</th>
                                    <th>{{ __('cash_register.opened_at') }}</th>
                                    <th class="text-end">{{ __('cash_register.opening_float') }}</th>
                                    <th class="text-end">{{ __('cash_register.expected_cash') }}</th>
                                    <th class="text-end">{{ __('cash_register.counted_amount') }}</th>
                                    <th class="text-end">{{ __('cash_register.difference') }}</th>
                                    <th>{{ __('cash_register.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sessions as $session)
                                    <tr wire:key="session-{{ $session->id }}">
                                        <td>{{ $session->user?->name }}</td>
                                        <td>{{ $session->opened_at->format('Y-m-d H:i') }}</td>
                                        <td class="text-end">{{ number_format($session->opening_float / 100, 2) }}</td>
                                        <td class="text-end">{{ $session->expected_amount !== null ? number_format($session->expected_amount / 100, 2) : '—' }}</td>
                                        <td class="text-end">{{ $session->closing_amount !== null ? number_format($session->closing_amount / 100, 2) : '—' }}</td>
                                        <td class="text-end {{ ($session->difference ?? 0) < 0 ? 'text-danger' : (($session->difference ?? 0) > 0 ? 'text-warning' : '') }}">
                                            {{ $session->difference !== null ? number_format($session->difference / 100, 2) : '—' }}
                                        </td>
                                        <td><span class="badge bg-{{ $session->status->color() }}">{{ $session->status->label() }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted">{{ __('cash_register.no_sessions') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center">{{ $sessions->links('pagination::bootstrap-5') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
