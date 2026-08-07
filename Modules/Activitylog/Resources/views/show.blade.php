@extends('layouts.app')

@section('title', 'Activity Log Details')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('activity-logs.index') }}">Activity Logs</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
@endsection

@php
    $changes = $activity->attribute_changes ? $activity->attribute_changes->toArray() : [];
    $newAttributes = $changes['attributes'] ?? [];
    $oldAttributes = $changes['old'] ?? [];
    $attributeKeys = array_keys($newAttributes + $oldAttributes);
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Activity Details</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Description</span><span>{{ ucfirst($activity->description) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Event</span><span>{{ ucfirst($activity->event ?? 'n/a') }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Module</span><span>{{ $activity->log_name ?? 'default' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Subject</span>
                                <span>
                                    @if($activity->subject_type)
                                        {{ \Illuminate\Support\Str::headline(class_basename($activity->subject_type)) }}
                                        #{{ $activity->subject_id }}
                                    @else
                                        &mdash;
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Performed By</span><span>{{ $activity->causer?->name ?? 'System' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Date &amp; Time</span><span>{{ $activity->created_at->format('d M, Y H:i:s') }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Attribute Changes</h5>
                    </div>
                    <div class="card-body">
                        @if(count($attributeKeys))
                            <div class="row">
                                @foreach($attributeKeys as $key)
                                    @php
                                        $old = $oldAttributes[$key] ?? null;
                                        $new = $newAttributes[$key] ?? null;
                                    @endphp
                                    <div class="col-md-6 mb-3">
                                        <div class="card border h-100">
                                            <div class="card-header py-2">
                                                <strong>{{ \Illuminate\Support\Str::headline($key) }}</strong>
                                            </div>
                                            <div class="card-body py-2">
                                                <div class="mb-1">
                                                    <small class="text-muted d-block">Old Value</small>
                                                    <span class="text-danger">{{ is_array($old) ? json_encode($old) : ($old ?? '—') }}</span>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">New Value</small>
                                                    <span class="text-success">{{ is_array($new) ? json_encode($new) : ($new ?? '—') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No attribute changes were recorded for this activity.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Activity Logs
                </a>
            </div>
        </div>
    </div>
@endsection
