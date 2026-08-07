@extends('layouts.app')

@section('title', __('roles.roles_and_permissions'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('roles.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('roles.roles_and_permissions') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('roles.create') }}" class="btn btn-primary">
                    {{ __('roles.add_role') }} <i class="bi bi-plus"></i>
                </a>
            </div>
        </div>

        <div class="row">
            @forelse($roles as $role)
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="fw-bold">{{ $role->name }}</span>
                            <span class="badge bg-secondary">#{{ $role->id }}</span>
                        </div>
                        <div class="card-body">
                            <p class="fw-bold mb-2">{{ __('roles.permissions') }}</p>
                            <div class="mb-3">
                                @include('user::roles.partials.permissions', ['data' => $role])
                            </div>
                            <div class="btn-group">
                                @include('user::roles.partials.actions', ['data' => $role])
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">{{ __('roles.no_roles_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $roles->links() }}
        </div>
    </div>
@endsection
