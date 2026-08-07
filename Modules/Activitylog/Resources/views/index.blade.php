@extends('layouts.app')

@section('title', 'Activity Logs')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Activity Logs</li>
    </ol>
@endsection

@php
    $eventBadges = [
        'created' => 'badge-success',
        'updated' => 'badge-info',
        'deleted' => 'badge-danger',
        'restored' => 'badge-warning',
    ];
@endphp

@section('content')
    <div class="container-fluid">
        @can('delete_activity_logs')
            <div class="row">
                <div class="col-12 mb-3">
                    <button type="button" class="btn btn-danger" onclick="
                        event.preventDefault();
                        {
                        document.getElementById('clear-activity-logs').submit();
                        }
                        ">
                        Clear All Logs <i class="bi bi-trash"></i>
                    </button>
                    <form id="clear-activity-logs" class="d-none" action="{{ route('activity-logs.clear') }}" method="POST">
                        @csrf
                        @method('delete')
                    </form>
                </div>
            </div>
        @endcan

        <div class="row">
            @forelse($activities as $activity)
                <div class="col-xl-4 col-lg-6 mb-4">
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
                                @include('activitylog::partials.actions', ['data' => $activity])
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">No activity logs found.</div></div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $activities->links() }}
        </div>
    </div>
@endsection
