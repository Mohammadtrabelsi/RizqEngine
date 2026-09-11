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

    {{-- Filters container --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
            <h5 class="mb-0"><i class="bi bi-funnel text-primary"></i> {{ __('app.filters') }}</h5>
        </div>
        <div class="flex-auto p-2">
            <div class="row align-items-end">
                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('users.role') }}</label>
                    <select wire:model.live="role" class="form-select">
                        <option value="">{{ __('app.all') }}</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label small text-muted mb-1">{{ __('users.status') }}</label>
                    <select wire:model.live="status" class="form-select">
                        <option value="">{{ __('app.all') }}</option>
                        <option value="1">{{ __('users.active') }}</option>
                        <option value="0">{{ __('users.deactivated') }}</option>
                    </select>
                </div>
                <div class="col-12 col-lg-4 mb-3">
                    <button type="button" wire:click="resetFilters" class="btn btn-secondary w-100">
                        <i class="bi bi-x-circle"></i> {{ __('app.reset') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $users->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($users as $user)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="user-{{ $user->id }}">
                <div class="card h-100">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">
                        <h5 class="mb-0">{{ $user->name }}</h5>
                    </div>
                    <div class="flex-auto p-2 text-center">
                        <img src="{{ $user->getFirstMediaUrl('avatars') }}" class="avatar-80 img-thumbnail rounded-circle mb-3 mt-2" alt="{{ $user->name }}">
                        <p class="text-muted mb-2"><small>{{ $user->email }}</small></p>
                        <div class="mb-2">
                            @include('user.users.partials.roles', ['roles' => $user->getRoleNames()])
                        </div>
                        <div class="mb-3">
                            @if($user->is_active == 1)
                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-emerald-100 text-emerald-700">{{ __('users.active') }}</span>
                            @else
                                <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-amber-100 text-amber-700">{{ __('users.deactivated') }}</span>
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
                <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('users.no_users_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
