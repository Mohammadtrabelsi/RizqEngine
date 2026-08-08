@extends('layouts.app')

@section('title', __('units.units'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('units.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('units.units') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('units.create') }}" class="btn btn-primary">
                    {{ __('units.add_unit') }} <i class="bi bi-plus"></i>
                </a>
            </div>
        </div>

        <div class="row">
            @forelse($units as $unit)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $unit->name }} <small class="text-muted">({{ $unit->short_name }})</small></h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('units.operator') }}</span><span>{{ $unit->operator }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('units.operation_value') }}</span><span>{{ $unit->operation_value }}</span></li>
                            </ul>
                            <div class="btn-group">
                                <a href="{{ route('units.edit', $unit) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button id="delete" class="btn btn-danger btn-sm delete-confirm" onclick="
                                    event.preventDefault();
                                    {
                                    document.getElementById('destroy{{ $unit->id }}').submit()
                                    }
                                    ">
                                    <i class="bi bi-trash"></i>
                                    <form id="destroy{{ $unit->id }}" class="d-none" action="{{ route('units.destroy', $unit) }}" method="POST">
                                        @csrf
                                        @method('delete')
                                    </form>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">{{ __('units.no_units_found') }}</div></div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
