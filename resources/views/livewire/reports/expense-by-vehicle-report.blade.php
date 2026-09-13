<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="flex-auto p-2">
                    <form wire:submit="generateReport">
                        <div class="form-row align-items-end">
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label>{{ __('reports.start_date') }} <span class="text-danger">*</span></label>
                                <input wire:model="start_date" type="date" class="form-control" name="start_date">
                                @error('start_date')
                                <span class="text-danger mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label>{{ __('reports.end_date') }} <span class="text-danger">*</span></label>
                                <input wire:model="end_date" type="date" class="form-control" name="end_date">
                                @error('end_date')
                                <span class="text-danger mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-auto mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <span wire:target="generateReport" wire:loading class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <i wire:target="generateReport" wire:loading.remove class="bi bi-shuffle"></i>
                                    {{ __('reports.filter_report') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($rows->isNotEmpty())
        <div class="row mb-2">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="relative flex flex-col min-w-0 break-words border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm h-100">
                    <div class="flex-auto p-2 d-flex align-items-center">
                        <div class="icon-tile-48 rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3">
                            <i class="bi bi-truck fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase">{{ __('expense.expenses') }}</div>
                            <div class="h4 mb-0 fw-bold">{{ number_format($summary['count']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="relative flex flex-col min-w-0 break-words border border-slate-200 rounded-xl shadow-sm text-slate-900 border-0 shadow-sm h-100">
                    <div class="flex-auto p-2 d-flex align-items-center">
                        <div class="icon-tile-48 rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase">{{ __('report.total') }}</div>
                            <div class="h4 mb-0 fw-bold">{{ format_currency($summary['total_amount']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="flex-auto p-2">
                        <div wire:loading.flex class="col-12 position-absolute justify-content-center align-items-center wire-loading-overlay">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <div class="block w-full overflow-x-auto">
                            <table class="report-table w-full mb-4 text-slate-900 border-collapse [&_tbody_tr:hover]:bg-slate-50 align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small text-uppercase">
                                        <th scope="col">{{ __('expense.vehicle') }}</th>
                                        <th scope="col" class="text-end">{{ __('expense.expenses') }}</th>
                                        <th scope="col" class="text-end">{{ __('report.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $row)
                                        <tr>
                                            <td class="fw-bold" data-label="{{ __('expense.vehicle') }}">{{ $row->label }}</td>
                                            <td class="text-end" data-label="{{ __('expense.expenses') }}">{{ number_format($row->count) }}</td>
                                            <td class="text-end" data-label="{{ __('report.total') }}">{{ format_currency($row->total_amount) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold border-top">
                                        <td>{{ __('report.total') }}</td>
                                        <td class="text-end">{{ number_format($summary['count']) }}</td>
                                        <td class="text-end">{{ format_currency($summary['total_amount']) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="flex-auto p-2">
                        <div class="relative px-5 py-3 mb-4 rounded-md border border-transparent bg-amber-50 text-amber-700 border-amber-200 mb-0">
                            {{ __('report.no-data-available') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
