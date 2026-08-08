@extends('layouts.app')

@section('title', __('adjustment.adjustments'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('adjustment.adjustments') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('adjustments.create') }}" class="btn btn-primary">
                    {{ __('adjustment.add_adjustment') }} <i class="bi bi-plus"></i>
                </a>
            </div>
        </div>

        <div class="row">
            @forelse($adjustments as $adjustment)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $adjustment->reference }}</h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('adjustment.date') }}</span><span>{{ $adjustment->date }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('adjustment.products') }}</span><span class="badge bg-info">{{ $adjustment->adjusted_products_count }}</span></li>
                            </ul>
                            <div class="btn-group">
                                @include('adjustment.partials.actions', ['data' => $adjustment])
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">{{ __('adjustment.no_adjustments_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $adjustments->links() }}
        </div>
    </div>
@endsection
