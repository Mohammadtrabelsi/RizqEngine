{{-- Full-page Livewire component: single root, shell provides chrome. --}}
<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-5 mb-4">
                @if($current)
                    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                        <div class="flex-auto p-5">
                            <h5 class="mb-3 text-lg font-semibold text-slate-900 d-flex justify-content-between align-items-center">
                                {{ __('cash_register.current_session') }}
                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-success">{{ __('cash_register.open') }}</span>
                            </h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('cash_register.opened_at') }}</span><span>{{ $current->opened_at->format('Y-m-d H:i') }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('cash_register.opening_float') }}</span><span>{{ number_format($current->opening_float / 100, 2) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('cash_register.cash_sales') }}</span><span>{{ number_format($cashSales / 100, 2) }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0 fw-bold"><span>{{ __('cash_register.expected_cash') }}</span><span>{{ number_format($expected / 100, 2) }}</span></li>
                            </ul>
                            @can('close_cash_register')
                            <form wire:submit="close">
                                <div class="mb-4">
                                    <label>{{ __('cash_register.counted_amount') }}</label>
                                    <input type="number" step="0.01" min="0" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('counted_amount') !border-red-500 @enderror" wire:model="counted_amount">
                                    @error('counted_amount') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-4">
                                    <label>{{ __('cash_register.note') }}</label>
                                    <textarea class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" rows="2" wire:model="note"></textarea>
                                </div>
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-red-500 !text-white border-red-500 hover:bg-red-600 hover:border-red-600 w-100">{{ __('cash_register.close_session') }} (Z)</button>
                            </form>
                            @endcan
                        </div>
                    </div>
                @else
                    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                        <div class="flex-auto p-5">
                            <h5 class="mb-3 text-lg font-semibold text-slate-900">{{ __('cash_register.open_session') }}</h5>
                            @can('open_cash_register')
                            <form wire:submit="open">
                                <div class="mb-4">
                                    <label>{{ __('cash_register.opening_float') }}</label>
                                    <input type="number" step="0.01" min="0" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('opening_float') !border-red-500 @enderror" wire:model="opening_float">
                                    @error('opening_float') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-4">
                                    <label>{{ __('cash_register.note') }}</label>
                                    <textarea class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" rows="2" wire:model="note"></textarea>
                                </div>
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700 w-100">{{ __('cash_register.open_session') }}</button>
                            </form>
                            @else
                                <p class="text-muted mb-0">{{ __('cash_register.no_open_session') }}</p>
                            @endcan
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-7 mb-4">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm">
                    <div class="flex-auto p-5 table-responsive">
                        <h5 class="mb-3 text-lg font-semibold text-slate-900">{{ __('cash_register.history') }}</h5>
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
                                        <td><span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-{{ $session->status->color() }}">{{ $session->status->label() }}</span></td>
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
