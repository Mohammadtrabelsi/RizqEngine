@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="relative flex flex-col min-w-0 break-words bg-white border border-slate-200 rounded-xl shadow-sm text-slate-900">
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 font-semibold text-slate-900 rounded-t-xl">{{ __('password.confirmation') }}</div>

                    <div class="flex-auto p-5">
                        {{ __('password.confirm-message') }}

                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

                            <div class="mb-4 row">
                                <label for="password"
                                       class="col-md-4 col-form-label text-md-right">
                                    {{ __('password.password') }}
                                </label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                           class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('password') !border-red-500 @enderror" name="password"
                                           required autocomplete="current-password">
                                    @error('password')
                                    <span class="block w-full mt-1 text-xs text-red-500" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4 row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
                                        {{ __('password.confirm') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="!text-indigo-600 bg-transparent border-transparent underline-offset-2 hover:underline px-2 cursor-pointer no-underline" href="{{ route('password.request') }}">
                                            {{ __('password.forgot') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
