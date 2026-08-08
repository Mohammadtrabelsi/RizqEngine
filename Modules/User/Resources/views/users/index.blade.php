@extends('layouts.app')

@section('title',   __('users.users'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('users.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('users.users') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    {{ __('users.add_user') }} <i class="bi bi-plus"></i>
                </a>
            </div>
        </div>

        <div class="row">
            @forelse($users as $user)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="{{ $user->getFirstMediaUrl('avatars') }}" style="width:80px;height:80px;" class="img-thumbnail rounded-circle mb-3" alt="{{ $user->name }}">
                            <h5 class="card-title mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-2"><small>{{ $user->email }}</small></p>
                            <div class="mb-2">
                                @include('user::users.partials.roles', ['roles' => $user->getRoleNames()])
                            </div>
                            <div class="mb-3">
                                @if($user->is_active == 1)
                                    <span class="badge badge-success">{{ __('users.active') }}</span>
                                @else
                                    <span class="badge badge-warning">{{ __('users.deactivated') }}</span>
                                @endif
                            </div>
                            <div class="btn-group">
                                @include('user::users.partials.actions', ['data' => $user])
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">{{ __('users.no_users_found') }}</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $users->links() }}
        </div>
    </div>
@endsection
