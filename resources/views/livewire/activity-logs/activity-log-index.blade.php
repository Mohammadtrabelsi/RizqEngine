<div>
    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            @can('delete_activity_logs')
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#clearLogsModal">
                    Clear All Logs <i class="bi bi-trash"></i>
                </button>
            @endcan
        </div>
        <div class="col-12 col-md-6 mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="{{ __('app.search') }}">
        </div>
    </div>

    <div class="d-flex justify-content-center mb-3">{{ $activities->links('pagination::bootstrap-5') }}</div>
    <div class="row">
        @forelse($activities as $activity)
            <div class="col-xl-4 col-lg-6 mb-4" wire:key="activity-{{ $activity->id }}">
                <div class="card h-100">
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
                            <a href="{{ route('activity-logs.show', $activity->id) }}" class="btn btn-outline btn-sm"><i class="bi bi-eye"></i></a>
                            @can('delete_activity_logs')
                                <button type="button" class="btn btn-outline-danger btn-sm" wire:click="delete({{ $activity->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="flex-auto p-2 text-center text-muted">{{ __('activitylog.no_activity_logs') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $activities->links('pagination::bootstrap-5') }}
    </div>

    @can('delete_activity_logs')
        <div class="modal fade" id="clearLogsModal" tabindex="-1" role="dialog" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clearLogsModalLabel">{{ __('activitylog.clear_all_logs') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{ __('activitylog.clear_all_logs_confirm') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('app.cancel') }}</button>
                        <button type="button" class="btn btn-danger" wire:click="clear" data-dismiss="modal">
                            {{ __('app.yes') }} <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endcan
</div>
