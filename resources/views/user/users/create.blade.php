@extends('layouts.app')

@section('title', __('users.add_user'))

@section('third_party_stylesheets')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet"/>
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css"
          rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('users.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('users.users') }}</a></li>
        <li class="breadcrumb-item active">{{ __('users.create') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    @include('utils.alerts')
                    <div class="mb-4">
                        <button class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">{{ __('users.create_user') }} <i class="bi bi-check"></i></button>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
                        <div class="flex-auto p-2">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="name">{{ __('users.name') }} <span class="text-danger">*</span></label>
                                        <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" type="text" name="name" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="email">{{ __('users.email') }} <span class="text-danger">*</span></label>
                                        <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" type="email" name="email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="password">{{ __('users.password') }} <span class="text-danger">*</span></label>
                                        <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" type="password" name="password" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="password_confirmation">{{ __('users.confirm_password') }} <span
                                                class="text-danger">*</span></label>
                                        <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" type="password" name="password_confirmation"
                                               required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="role">{{ __('users.role') }} <span class="text-danger">*</span></label>
                                <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="role" id="role" required>
                                    <option value="" selected disabled>{{ __('users.select_role') }}</option>
                                    @foreach(\Spatie\Permission\Models\Role::where('name', '!=', 'Super Admin')->get() as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="is_active">{{ __('users.status') }} <span class="text-danger">*</span></label>
                                <select class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45" name="is_active" id="is_active" required>
                                    <option value="" selected disabled>{{ __('users.select_status') }}</option>
                                    <option value="1">{{ __('users.active') }}</option>
                                    <option value="2">{{ __('users.deactivated') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
                        <div class="flex-auto p-2">
                            <div class="mb-4">
                                <label for="image">{{ __('users.profile_image') }} <span class="text-danger">*</span></label>
                                <input id="image" type="file" name="image" data-max-file-size="500KB">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection


@push('page_scripts')
    @include('includes.filepond-js')
@endpush


