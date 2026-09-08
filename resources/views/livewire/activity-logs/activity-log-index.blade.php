<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            @can('delete_activity_logs')
                <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-red-500 !text-white border-red-500 hover:bg-red-600 hover:border-red-600" wire:click="clear" wire:confirm="{{ __('app.are_you_sure') }}">
                    Clear All Logs <i class="bi bi-trash"></i>
                </button>
            @endcan
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $activities->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($activities as $activity)
            <div class="col-xl-4 col-lg-6 mb-4" wire:key="activity-{{ $activity->id }}">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900 h-100">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl d-flex justify-content-between align-items-center">
                        <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline {{ $this->eventBadge($activity->event) }}">{{ $activity->event ?? 'n/a' }}</span>
                        <small class="text-muted">{{ $activity->created_at->format('d M, Y H:i') }}</small>
                    </div>
                    <div class="flex-auto p-2">
                        <p class="mb-2">{{ $activity->description }}</p>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>Module</span><span>{{ $activity->log_name }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>Subject</span>
                                <span>
                                    @if($activity->subject_type)
                                        <span class="inline-block rounded-md px-1.5 py-0.5 text-xs font-semibold leading-none text-center whitespace-nowrap align-baseline bg-slate-100 text-slate-600">{{ \Illuminate\Support\Str::headline(class_basename($activity->subject_type)) }} #{{ $activity->subject_id }}</span>
                                    @else
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>User</span><span>{{ $activity->causer->name ?? 'System' }}</span></li>
                        </ul>
                        <div class="btn-group">
                            <a href="{{ route('activity-logs.show', $activity->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-indigo-600 border-indigo-600 hover:bg-indigo-600 hover:!text-white !px-3 !py-1.5 !text-xs"><i class="bi bi-eye"></i></a>
                            @can('delete_activity_logs')
                                <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default !text-red-500 border-red-500 hover:bg-red-500 hover:!text-white !px-3 !py-1.5 !text-xs" wire:click="delete({{ $activity->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900"><div class="flex-auto p-2 text-center text-muted">{{ __('activitylog.no_activity_logs') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $activities->links('pagination::bootstrap-5') }}
    </div>
</div>
