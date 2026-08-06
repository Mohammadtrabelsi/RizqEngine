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
                        <table class="table table-striped mb-0">
                            <tbody>
                                <tr>
                                    <th style="width: 40%;">Description</th>
                                    <td>{{ ucfirst($activity->description) }}</td>
                                </tr>
                                <tr>
                                    <th>Event</th>
                                    <td>{{ ucfirst($activity->event ?? 'n/a') }}</td>
                                </tr>
                                <tr>
                                    <th>Module</th>
                                    <td>{{ $activity->log_name ?? 'default' }}</td>
                                </tr>
                                <tr>
                                    <th>Subject</th>
                                    <td>
                                        @if($activity->subject_type)
                                            {{ \Illuminate\Support\Str::headline(class_basename($activity->subject_type)) }}
                                            #{{ $activity->subject_id }}
                                        @else
                                            &mdash;
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Performed By</th>
                                    <td>{{ $activity->causer?->name ?? 'System' }}</td>
                                </tr>
                                <tr>
                                    <th>Date &amp; Time</th>
                                    <td>{{ $activity->created_at->format('d M, Y H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
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
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Attribute</th>
                                            <th>Old Value</th>
                                            <th>New Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($attributeKeys as $key)
                                            <tr>
                                                <td><strong>{{ \Illuminate\Support\Str::headline($key) }}</strong></td>
                                                <td>
                                                    @php $old = $oldAttributes[$key] ?? null; @endphp
                                                    <span class="text-danger">
                                                        {{ is_array($old) ? json_encode($old) : ($old ?? '—') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php $new = $newAttributes[$key] ?? null; @endphp
                                                    <span class="text-success">
                                                        {{ is_array($new) ? json_encode($new) : ($new ?? '—') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
