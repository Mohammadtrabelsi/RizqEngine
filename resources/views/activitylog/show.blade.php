@extends('layouts.app')

@section('title', __('activitylog.activity_log_details'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('app.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('activity-logs.index') }}">{{ __('activitylog.activity_logs') }}</a></li>
        <li class="breadcrumb-item active">{{ __('activitylog.activity_log_details') }}</li>
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
                        <h5 class="mb-0">{{ __('activitylog.activity_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('activitylog.description') }}</span><span>{{ ucfirst($activity->description) }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('activitylog.event') }}</span><span>{{ ucfirst($activity->event ?? 'n/a') }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('activitylog.module') }}</span><span>{{ $activity->log_name ?? 'default' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">{{ __('activitylog.subject') }}</span>
                                <span>
                                    @if($activity->subject_type)
                                        {{ \Illuminate\Support\Str::headline(class_basename($activity->subject_type)) }}
                                        #{{ $activity->subject_id }}
                                    @else
                                        &mdash;
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('activitylog.performed_by') }}</span><span>{{ $activity->causer?->name ?? 'System' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">{{ __('activitylog.date_time') }}</span><span>{{ $activity->created_at->format('d M, Y H:i:s') }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('activitylog.attribute_changes') }}</h5>
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
                                                    <small class="text-muted d-block">{{ __('activitylog.old_value') }}</small>
                                                    <span class="text-danger">{{ is_array($old) ? json_encode($old) : ($old ?? '—') }}</span>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">{{ __('activitylog.new_value') }}</small>
                                                    <span class="text-success">{{ is_array($new) ? json_encode($new) : ($new ?? '—') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">{{ __('activitylog.no_attribute_changes') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> {{ __('activitylog.back_to_activity_logs') }}
                </a>
            </div>
        </div>
    </div>
@endsection
