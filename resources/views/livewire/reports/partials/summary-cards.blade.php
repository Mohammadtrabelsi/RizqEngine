{{--
    Summary KPI cards for a report.

    Expects:
      $summary   array{count:int, total_amount:float, paid_amount:float, due_amount:float}
      $countLabel string  Label for the record count card (e.g. "Sales").
--}}
<div class="row mb-2">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon-tile-48 rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3">
                    <i class="bi bi-collection fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase">{{ $countLabel }}</div>
                    <div class="h4 mb-0 fw-bold">{{ number_format($summary['count']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon-tile-48 rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center me-3">
                    <i class="bi bi-cash-stack fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase">{{ __('report.total') }}</div>
                    <div class="h4 mb-0 fw-bold">{{ format_currency($summary['total_amount']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon-tile-48 rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3">
                    <i class="bi bi-check2-circle fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase">{{ __('report.paid') }}</div>
                    <div class="h4 mb-0 fw-bold">{{ format_currency($summary['paid_amount']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon-tile-48 rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center me-3">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase">{{ __('report.due') }}</div>
                    <div class="h4 mb-0 fw-bold">{{ format_currency($summary['due_amount']) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
