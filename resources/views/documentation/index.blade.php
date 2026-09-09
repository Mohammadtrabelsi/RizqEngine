@extends('layouts.app')

@section('title', __('documentation.documentation'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('documentation.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('documentation.documentation') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid py-4">

        {{-- Hero --}}
        <div class="mb-6 overflow-hidden rounded-xl border border-hairline bg-white shadow-sm">
            <div class="flex flex-col gap-4 p-6 sm:flex-row sm:items-center">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-accent-light text-accent">
                    <i class="bi bi-book text-3xl"></i>
                </span>
                <div>
                    <h1 class="mb-1 text-2xl font-semibold text-ink">{{ __('documentation.page_title') }}</h1>
                    <p class="mb-0 max-w-3xl text-muted">{{ __('documentation.intro') }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[240px_minmax(0,1fr)]">

            {{-- Sidebar nav (hidden on small screens; cards below act as nav there) --}}
            <aside class="hidden lg:block">
                <div class="sticky top-4 rounded-xl border border-hairline bg-white p-4 shadow-sm">
                    @include('documentation.partials.nav', ['navGroups' => $navGroups, 'current' => null])
                </div>
            </aside>

            {{-- Grouped topic cards --}}
            <div class="space-y-8">
                <div>
                    <h2 class="text-lg font-semibold text-ink">{{ __('documentation.overview_heading') }}</h2>
                    <p class="text-sm text-muted">{{ __('documentation.overview_subheading') }}</p>
                </div>

                @foreach ($groups as $group)
                    <section aria-label="{{ $group['label'] }}">
                        <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-ink-3">{{ $group['label'] }}</h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($group['sections'] as $section)
                                <a href="{{ route('documentation.show', $section['key']) }}"
                                   class="group flex h-full flex-col rounded-xl border border-hairline bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-accent hover:shadow-md">
                                    <span class="mb-3 flex h-11 w-11 items-center justify-center rounded-lg bg-accent-light text-accent transition group-hover:bg-accent group-hover:text-white">
                                        <i class="bi {{ $section['icon'] }} text-xl"></i>
                                    </span>
                                    <h4 class="mb-1 font-semibold text-ink">{{ $section['title'] }}</h4>
                                    <p class="mb-3 flex-1 text-sm text-muted">{{ $section['summary'] }}</p>
                                    <span class="inline-flex items-center gap-1 text-sm font-medium text-accent">
                                        {{ __('documentation.read_guide') }}
                                        <i class="bi bi-arrow-right transition group-hover:translate-x-0.5 rtl:hidden"></i>
                                        <i class="bi bi-arrow-left transition group-hover:-translate-x-0.5 ltr:hidden"></i>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endforeach

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
