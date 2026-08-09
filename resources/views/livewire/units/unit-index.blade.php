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
            <div class="col-12 col-md-6 mb-3">
                <a href="{{ route('units.create') }}" class="btn btn-primary">
                    {{ __('units.add_unit') }} <i class="bi bi-plus"></i>
                </a>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
            </div>
        </div>

        <div class="row">
            @forelse($units as $unit)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="unit-{{ $unit->id }}">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $unit->name }} <small class="text-muted">({{ $unit->short_name }})</small></h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('units.operator') }}</span><span>{{ $unit->operator }}</span></li>
                                <li class="list-group-item d-flex justify-content-between px-0"><span>{{ __('units.operation_value') }}</span><span>{{ $unit->operation_value }}</span></li>
                            </ul>
                            <div class="btn-group">
                                <a href="{{ route('units.edit', $unit) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $unit->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
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

        <div class="d-flex justify-content-center">
            {{ $units->links() }}
        </div>
    </div>
@endsection
