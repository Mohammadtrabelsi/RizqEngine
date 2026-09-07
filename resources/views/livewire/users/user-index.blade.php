<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                {{ __('users.add_user') }} <i class="bi bi-plus"></i>
            </a>
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $users->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($users as $user)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" wire:key="user-{{ $user->id }}">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 h-100">
                    <div class="flex-auto p-5 text-center">
                        <img src="{{ $user->getFirstMediaUrl('avatars') }}" class="avatar-80 img-thumbnail rounded-circle mb-3" alt="{{ $user->name }}">
                        <h5 class="mb-3 text-lg font-semibold text-slate-900 mb-1">{{ $user->name }}</h5>
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
                            <a href="{{ route('users.edit', $user->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-cyan-500 !text-white border-cyan-500 hover:bg-cyan-600 !px-3 !py-1.5 !text-xs"><i class="bi bi-pencil"></i></a>
                            <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-red-500 !text-white border-red-500 hover:bg-red-600 hover:border-red-600 !px-3 !py-1.5 !text-xs" wire:click="delete({{ $user->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900"><div class="flex-auto p-5 text-center text-muted">{{ __('users.no_users_found') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
