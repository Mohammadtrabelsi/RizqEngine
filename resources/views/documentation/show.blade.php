@extends('layouts.app')

@section('title', $section['title'])

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('documentation.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('documentation.index') }}">{{ __('documentation.documentation') }}</a></li>
        <li class="breadcrumb-item active">{{ $section['title'] }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[240px_minmax(0,1fr)]">

            {{-- Sidebar nav --}}
            <aside class="hidden lg:block">
                <div class="sticky top-4 rounded-xl border border-hairline bg-white p-4 shadow-sm">
                    @include('documentation.partials.nav', ['navGroups' => $navGroups, 'current' => $key])
                </div>
            </aside>

            <div class="space-y-6">

                <a href="{{ route('documentation.index') }}"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-muted hover:text-accent">
                    <i class="bi bi-arrow-left rtl:hidden"></i>
                    <i class="bi bi-arrow-right ltr:hidden"></i>
                    {{ __('documentation.back_to_overview') }}
                </a>

                {{-- Topic article --}}
                <article class="rounded-xl border border-hairline bg-white shadow-sm">
                    <header class="flex items-start gap-4 border-b border-hairline p-6">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-accent-light text-accent">
                            <i class="bi {{ $section['icon'] }} text-2xl"></i>
                        </span>
                        <div>
                            <h1 class="mb-1 text-2xl font-semibold text-ink">{{ $section['title'] }}</h1>
                            <p class="mb-0 text-muted">{{ $section['summary'] }}</p>
                        </div>
                    </header>

                    <div class="space-y-4 p-6 text-ink">
                        @foreach ($section['body'] as $paragraph)
                            <p class="mb-0 leading-relaxed">{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </article>

                {{-- Previous / next --}}
                @if ($previous || $next)
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @if ($previous)
                            <a href="{{ route('documentation.show', $previous['key']) }}"
                               class="group flex items-center gap-3 rounded-xl border border-hairline bg-white p-4 shadow-sm transition hover:border-accent">
                                <i class="bi bi-arrow-left text-lg text-accent rtl:hidden"></i>
                                <i class="bi bi-arrow-right text-lg text-accent ltr:hidden"></i>
                                <span class="min-w-0">
                                    <span class="block text-xs uppercase tracking-wide text-ink-3">{{ __('documentation.previous') }}</span>
                                    <span class="block truncate font-medium text-ink group-hover:text-accent">{{ $previous['title'] }}</span>
                                </span>
                            </a>
                        @else
                            <span></span>
                        @endif

                        @if ($next)
                            <a href="{{ route('documentation.show', $next['key']) }}"
                               class="group flex items-center justify-end gap-3 rounded-xl border border-hairline bg-white p-4 text-end shadow-sm transition hover:border-accent">
                                <span class="min-w-0">
                                    <span class="block text-xs uppercase tracking-wide text-ink-3">{{ __('documentation.next') }}</span>
                                    <span class="block truncate font-medium text-ink group-hover:text-accent">{{ $next['title'] }}</span>
                                </span>
                                <i class="bi bi-arrow-right text-lg text-accent rtl:hidden"></i>
                                <i class="bi bi-arrow-left text-lg text-accent ltr:hidden"></i>
                            </a>
                        @endif
                    </div>
                @endif

                {{-- Help callout --}}
                <div class="rounded-xl border border-accent-soft bg-accent-light p-5">
                    <h4 class="mb-1 flex items-center gap-2 font-semibold text-ink">
                        <i class="bi bi-question-circle text-accent"></i>
                        {{ __('documentation.need_help') }}
                    </h4>
                    <p class="mb-0 text-sm text-muted">{{ __('documentation.need_help_body') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
