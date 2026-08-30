<div>
    @php
        $eventBadges = [
            'created' => 'badge-success',
            'updated' => 'badge-info',
            'deleted' => 'badge-danger',
            'restored' => 'badge-warning',
        ];
    @endphp

    <div class="row">
        <div class="col-12 col-md-6 mb-3">
            @can('delete_activity_logs')
                <button type="button" class="btn btn-danger" wire:click="clear" wire:confirm="{{ __('app.are_you_sure') }}">
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge {{ $eventBadges[$activity->event] ?? 'badge-secondary' }}">{{ $activity->event ?? 'n/a' }}</span>
                        <small class="text-muted">{{ $activity->created_at->format('d M, Y H:i') }}</small>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">{{ $activity->description }}</p>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0"><span>Module</span><span>{{ $activity->log_name }}</span></li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>Subject</span>
                                <span>
                                    @if($activity->subject_type)
                                        <span class="badge badge-light">{{ \Illuminate\Support\Str::headline(class_basename($activity->subject_type)) }} #{{ $activity->subject_id }}</span>
                                    @else
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0"><span>User</span><span>{{ $activity->causer->name ?? 'System' }}</span></li>
                        </ul>
                        <div class="btn-group">
                            <a href="{{ route('activity-logs.show', $activity->id) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                            @can('delete_activity_logs')
                                <button type="button" class="btn btn-outline-danger btn-sm" wire:click="delete({{ $activity->id }})" wire:confirm="{{ __('app.are_you_sure') }}"><i class="bi bi-trash"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-muted">{{ __('activitylog.no_activity_logs') }}</div></div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $activities->links('pagination::bootstrap-5') }}
    </div>
</div>
