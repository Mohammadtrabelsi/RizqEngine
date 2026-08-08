@extends('layouts.app')

@section('title', __('documentation.documentation'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('documentation.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('documentation.documentation') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-book display-4 text-primary mb-0 mr-3"></i>
                        <div>
                            <h4 class="mb-1">{{ __('documentation.page_title') }}</h4>
                            <p class="mb-0 text-muted">{{ __('documentation.intro') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 mb-4">
                <div class="card sticky-top" style="top: 80px;">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-list-ul"></i> {{ __('documentation.toc') }}</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach ($sections as $section)
                            <a href="#{{ $section }}" class="list-group-item list-group-item-action">
                                {{ __('documentation.sections.' . $section . '.title') }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                @foreach ($sections as $section)
                    <div class="card" id="{{ $section }}">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('documentation.sections.' . $section . '.title') }}</h5>
                        </div>
                        <div class="card-body">
                            @foreach (__('documentation.sections.' . $section . '.body') as $paragraph)
                                <p class="{{ $loop->last ? 'mb-0' : '' }}">{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="card border-primary">
                    <div class="card-body">
                        <h5 class="mb-1"><i class="bi bi-question-circle text-primary"></i> {{ __('documentation.need_help') }}</h5>
                        <p class="mb-0 text-muted">{{ __('documentation.need_help_body') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
