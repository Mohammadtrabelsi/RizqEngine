<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="flex-auto p-2">
                    <form wire:submit="generateReport">
                        <div class="form-row align-items-end">
                            <div class="col-lg-3 col-md-4 mb-3">
                                <label>{{ __('report.start-date') }} <span class="text-danger">*</span></label>
                                <input wire:model="start_date" type="date" class="form-control" name="start_date">
                                @error('start_date')
                                <span class="text-danger mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-3 col-md-4 mb-3">
                                <label>{{ __('report.end-date') }} <span class="text-danger">*</span></label>
                                <input wire:model="end_date" type="date" class="form-control" name="end_date">
                                @error('end_date')
                                <span class="text-danger mt-1">{{ $message }}</span>
                                @enderror
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
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-bold">{{ __('reports.vat_collected') }}</div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('reports.vat_rate') }}</th>
                                <th class="text-end">{{ __('reports.vat_base') }}</th>
                                <th class="text-end">{{ __('reports.vat_amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['collected'] as $line)
                                <tr>
                                    <td>{{ rtrim(rtrim(number_format($line['rate'], 2), '0'), '.') }}%</td>
                                    <td class="text-end">{{ format_currency($line['base']) }}</td>
                                    <td class="text-end">{{ format_currency($line['vat']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">{{ __('reports.vat_no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td>{{ __('reports.vat_total') }}</td>
                                <td></td>
                                <td class="text-end">{{ format_currency($report['collected_total']) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-bold">{{ __('reports.vat_deductible') }}</div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('reports.vat_rate') }}</th>
                                <th class="text-end">{{ __('reports.vat_base') }}</th>
                                <th class="text-end">{{ __('reports.vat_amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['deductible'] as $line)
                                <tr>
                                    <td>{{ rtrim(rtrim(number_format($line['rate'], 2), '0'), '.') }}%</td>
                                    <td class="text-end">{{ format_currency($line['base']) }}</td>
                                    <td class="text-end">{{ format_currency($line['vat']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">{{ __('reports.vat_no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td>{{ __('reports.vat_total') }}</td>
                                <td></td>
                                <td class="text-end">{{ format_currency($report['deductible_total']) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <span class="fw-bold fs-5">
                        {{ $report['is_credit'] ? __('reports.vat_credit') : __('reports.vat_due') }}
                    </span>
                    <span class="fw-bold fs-4 {{ $report['is_credit'] ? 'text-success' : 'text-danger' }}">
                        {{ format_currency(abs($report['vat_due'])) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
