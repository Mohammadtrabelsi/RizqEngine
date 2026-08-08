<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                {{ __('users.add_user') }} <i class="bi bi-plus"></i>
            </a>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="row">
        @forelse($users as $user)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="user-{{ $user->id }}">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="{{ $user->getFirstMediaUrl('avatars') }}" style="width:80px;height:80px;" class="img-thumbnail rounded-circle mb-3" alt="{{ $user->name }}">
                        <h5 class="card-title mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-2"><small>{{ $user->email }}</small></p>
                        <div class="mb-2">
                            @include('user.users.partials.roles', ['roles' => $user->getRoleNames()])
                        </div>
                        <div class="mb-3">
                            @if($user->is_active == 1)
                                <span class="badge badge-success">{{ __('users.active') }}</span>
                            @else
                                <span class="badge badge-warning">{{ __('users.deactivated') }}</span>
                            @endif
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-info btn-sm"><i class="bi bi-pencil"></i></a>
                            <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $user->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
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
