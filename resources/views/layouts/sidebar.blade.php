<div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show {{ request()->routeIs('app.pos.*') ? 'c-sidebar-minimized' : '' }}" id="sidebar">
    <style>
        /* Minimal overrides to give the sidebar a simpler, button-like look */
        .c-sidebar .simple-sidebar .c-sidebar-nav-link.sidebar-btn { display:flex; align-items:center; gap:10px; padding:8px 12px; border-radius:8px; color:var(--cui-sidebar-color, #fff); }
        .c-sidebar .simple-sidebar .c-sidebar-nav-link.sidebar-btn:hover { background: rgba(255,255,255,0.04); text-decoration:none; }
        .c-sidebar .simple-sidebar .c-sidebar-nav-item + .c-sidebar-nav-item { margin-top:4px; }
        .sidebar-footer { display:flex; gap:8px; padding:12px; justify-content:center; }
        .sidebar-footer .btn { font-size:13px; padding:6px 10px; }
    </style>
    @php($appSettings = settings())
    <div class="c-sidebar-brand d-md-down-none">
        <a class="c-sidebar-brand-link" href="{{ route('home') }}">
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
    <ul class="c-sidebar-nav simple-sidebar">
        @include('layouts.menu')
        <div class="ps__rail-x">
            <div class="ps__thumb-x" tabindex="0"></div>
        </div>
        <div class="ps__rail-y">
            <div class="ps__thumb-y" tabindex="0"></div>
        </div>
    </ul>
</ul>
    <div class="sidebar-footer">
        <button id="sidebar-toggle" class="btn btn-sm btn-outline-light" type="button" aria-label="Toggle sidebar">Toggle</button>
        <button id="sidebar-close" class="btn btn-sm btn-light" type="button" aria-label="Close sidebar">Close</button>
    </div>
</div>

<script>
    (function(){
        // Simple JS to toggle the CoreUI minimized class and hide sidebar
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebar-toggle');
        const closeBtn = document.getElementById('sidebar-close');
        if(toggle){
            toggle.addEventListener('click', function(){
                sidebar.classList.toggle('c-sidebar-minimized');
            });
        }
        if(closeBtn){
            closeBtn.addEventListener('click', function(){
                sidebar.style.display = sidebar.style.display === 'none' ? '' : 'none';
            });
        }
    })();
</script>
</div>
