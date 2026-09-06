<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form wire:submit="generateReport">
                        <div class="form-row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('report.start-date') }} <span class="text-danger">*</span></label>
                                    <input wire:model="start_date" type="date" class="form-control" name="start_date">
                                    @error('start_date')
                                        <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('report.end-date') }} <span class="text-danger">*</span></label>
                                    <input wire:model="end_date" type="date" class="form-control" name="end_date">
                                    @error('end_date')
                                        <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('report.group-by') }} <span class="text-danger">*</span></label>
                                    <select wire:model="grouping" class="form-control" name="grouping">
                                        <option value="day">{{ __('report.daily') }}</option>
                                        <option value="week">{{ __('report.weekly') }}</option>
                                        <option value="month">{{ __('report.monthly') }}</option>
                                    </select>
                                    @error('grouping')
                                        <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
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

    {{-- Summary cards --}}
    <div class="row mb-4">
        <div class="col-12 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-success p-3 mfe-3 rounded">
                        <i class="bi bi-receipt font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-success">{{ format_currency($totals['sales']) }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.sales') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-warning p-3 mfe-3 rounded">
                        <i class="bi bi-bag font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-warning">{{ format_currency($totals['purchases']) }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.purchases') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-info p-3 mfe-3 rounded">
                        <i class="bi bi-box-arrow-up-right font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-info">{{ format_currency($totals['outings']) }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.outings') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-danger p-3 mfe-3 rounded">
                        <i class="bi bi-wallet2 font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-danger">{{ format_currency($totals['expenses']) }}</div>
                        <div class="text-uppercase font-weight-bold small">{{ __('report.expenses') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed table --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('report.period') }}</th>
                                    <th class="text-right">{{ __('report.sales') }}</th>
                                    <th class="text-right">{{ __('report.purchases') }}</th>
                                    <th class="text-right">{{ __('report.outings') }}</th>
                                    <th class="text-right">{{ __('report.expenses') }}</th>
                                    <th class="text-right">{{ __('report.balance') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rows as $row)
                                    <tr>
                                        <td>{{ $row['label'] }}</td>
                                        <td class="text-right">{{ format_currency($row['sales']) }}</td>
                                        <td class="text-right">{{ format_currency($row['purchases']) }}</td>
                                        <td class="text-right">{{ format_currency($row['outings']) }}</td>
                                        <td class="text-right">{{ format_currency($row['expenses']) }}</td>
                                        <td class="text-right font-weight-bold {{ $row['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ format_currency($row['balance']) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">{{ __('report.no-data-available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if (! empty($rows))
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td>{{ __('report.total') }}</td>
                                        <td class="text-right">{{ format_currency($totals['sales']) }}</td>
                                        <td class="text-right">{{ format_currency($totals['purchases']) }}</td>
                                        <td class="text-right">{{ format_currency($totals['outings']) }}</td>
                                        <td class="text-right">{{ format_currency($totals['expenses']) }}</td>
                                        <td class="text-right {{ $totals['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ format_currency($totals['balance']) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
