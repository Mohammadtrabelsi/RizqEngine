<div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show {{ request()->routeIs('app.pos.*') ? 'c-sidebar-minimized' : '' }}" id="sidebar">
    @php($appSettings = settings())
    <div class="c-sidebar-brand d-md-down-none">
        <a href="{{ route('home') }}" style="text-decoration:none; color:var(--color-text);">
            <span class="c-sidebar-brand-full">
                @if($appSettings->client_logo || $appSettings->client_name)
                    <span style="display:inline-flex; align-items:center; gap:6px; line-height:1;">
                        @if($appSettings->client_logo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($appSettings->client_logo) }}" alt="{{ $appSettings->client_name ?? 'Logo' }}" style="max-height:26px; max-width:120px;">
                        @endif
                        @if($appSettings->client_name)
                            <span style="font-family:var(--font-heading, 'Inter',sans-serif); font-weight:500; font-size:14px; letter-spacing:0.01em;">{{ $appSettings->client_name }}</span>
                        @endif
                    </span>
                @else
                    @include('layouts.logo', ['size' => 22, 'label' => 'Triangle POS', 'labelSize' => 14])
                @endif
            </span>
            <span class="c-sidebar-brand-minimized">
                @if($appSettings->client_logo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($appSettings->client_logo) }}" alt="{{ $appSettings->client_name ?? 'Logo' }}" style="max-height:28px; max-width:40px;">
                @else
                    @include('layouts.logo', ['size' => 26])
                @endif
            </span>
        </a>
    </div>
    <ul class="c-sidebar-nav">
        @include('layouts.menu')
        <div class="ps__rail-x">
            <div class="ps__thumb-x" tabindex="0"></div>
        </div>
        <div class="ps__rail-y">
            <div class="ps__thumb-y" tabindex="0"></div>
        </div>
    </ul>
    <button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent" data-class="c-sidebar-minimized"></button>
</div>
