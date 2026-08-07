@extends('layouts.app')

@section('title', 'Activity Logs')

@section('third_party_stylesheets')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Activity Logs</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @can('delete_activity_logs')
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

                            <hr>
                        @endcan

                        <div class="table-responsive">
                            {!! $dataTable->table() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    {!! $dataTable->scripts() !!}
@endpush
