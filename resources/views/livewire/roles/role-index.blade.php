<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                {{ __('roles.add_role') }} <i class="bi bi-plus"></i>
            </a>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $roles->links() }}</div>
    <div class="row">
        @forelse($roles as $role)
            <div class="col-xl-4 col-lg-6 mb-4" wire:key="role-{{ $role->id }}">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fw-bold">{{ $role->name }}</span>
                        <span class="badge bg-secondary">#{{ $role->id }}</span>
                    </div>
                    <div class="card-body">
                        <p class="fw-bold mb-2">{{ __('roles.permissions') }}</p>
                        <div class="mb-3">
                            @include('user.roles.partials.permissions', ['data' => $role])
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-info btn-sm"><i class="bi bi-pencil"></i></a>
                            <button type="button" class="btn btn-danger btn-sm" wire:click="delete({{ $role->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
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
