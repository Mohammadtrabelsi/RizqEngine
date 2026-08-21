<div class="container-fluid d-flex align-items-center py-2 bg-white border-bottom">
    <button class="c-header-toggler app-nav-toggler d-lg-none mfe-auto btn btn-ghost text-slate-800" type="button"
            data-toggle="collapse" data-target="#app-topnav-collapse"
            aria-controls="app-topnav-collapse" aria-expanded="false" aria-label="Toggle navigation">
        <i class="bi bi-list icon-fs-2rem"></i>
    </button>

    @php($headerSettings = settings())
    <a href="{{ route('home') }}" class="c-header-brand d-flex align-items-center mfs-3" style="text-decoration:none; gap:8px;">
        @if($headerSettings->client_logo || $headerSettings->client_name)
            @if($headerSettings->client_logo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($headerSettings->client_logo) }}" alt="{{ $headerSettings->client_name ?? 'Logo' }}" style="max-height:32px; max-width:140px;">
            @endif
            @if($headerSettings->client_name)
                <span class="font-weight-bold d-md-down-none text-slate-900" style="font-size:1.05rem;">{{ $headerSettings->client_name }}</span>
            @endif
        @else
            @include('layouts.logo', ['size' => 24, 'label' => 'Triangle POS', 'labelSize' => 15])
        @endif
    </a>

    <ul class="c-header-nav ms-auto align-items-center gap-3">
        <li class="c-header-nav-item d-flex align-items-center">
            @include('includes.language-switcher')
        </li>

        @can('create_pos_sales')
            <li class="c-header-nav-item">
                <a class="btn btn-primary btn-pill {{ request()->routeIs('app.pos.index') ? 'disabled' : '' }}" href="{{ route('app.pos.index') }}">
                    <i class="bi bi-cart mr-1"></i> {{ __('app.pos_system') }}
                </a>
            </li>
        @endcan

        @can('show_notifications')
            <li class="c-header-nav-item dropdown d-md-down-none">
                {{-- Bell trigger --}}
                <a class="c-header-nav-link position-relative d-flex align-items-center justify-content-center"
                   style="width:38px; height:38px; border-radius:50%; background:#f1f5f9; color:#475569; transition:all .15s ease;"
                   data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="bi bi-bell" style="font-size:1rem;"></i>
                    @if($low_quantity_products->count() > 0)
                        <span class="position-absolute d-flex align-items-center justify-content-center"
                              style="top:-2px; right:-2px; width:18px; height:18px; border-radius:50%; background:#ef4444; color:#fff; font-size:10px; font-weight:700; line-height:1; border:2px solid #fff;">
                            {{ $low_quantity_products->count() > 9 ? '9+' : $low_quantity_products->count() }}
                        </span>
                    @endif
                </a>

                {{-- Dropdown panel --}}
                <div class="dropdown-menu dropdown-menu-right p-0 border-0 bg-white"
                     style="width:340px; border-radius:12px; box-shadow:0 10px 40px rgba(15,23,42,.12), 0 2px 8px rgba(15,23,42,.06); overflow:hidden;">

                    {{-- Panel header --}}
                    <div class="d-flex align-items-center justify-content-between px-3 py-3"
                         style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex align-items-center justify-content-center"
                                 style="width:30px; height:30px; border-radius:8px; background:#eef2ff;">
                                <i class="bi bi-bell-fill" style="color:#4f46e5; font-size:0.85rem;"></i>
                            </div>
                            <div>
                                <div class="font-weight-bold text-slate-900" style="font-size:0.88rem; line-height:1.2;">{{ __('app.notifications') }}</div>
                                <div class="text-slate-500" style="font-size:0.75rem;">{{ __('app.low-stock-alerts') }}</div>
                            </div>
                        </div>
                        @if($low_quantity_products->count() > 0)
                            <span style="background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; border-radius:20px; padding:2px 10px; font-size:11px; font-weight:600;">
                                {{ $low_quantity_products->count() }} {{ __('app.alerts') }}
                            </span>
                        @else
                            <span style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; border-radius:20px; padding:2px 10px; font-size:11px; font-weight:600;">
                                {{ __('app.all-clear') }}
                            </span>
                        @endif
                    </div>

                    {{-- Notification items --}}
                    <div style="max-height:320px; overflow-y:auto;">
                        @forelse($low_quantity_products as $product)
                            @php
                                $qty       = $product->product_quantity;
                                $alert     = $product->product_stock_alert;
                                $critical  = $qty <= $alert;
                                $lowStock  = !$critical && $qty <= ($alert * 2);
                                if ($critical) {
                                    $bg  = 'background:#fef2f2;';
                                    $clr = 'color:#dc2626;';
                                    $ico = 'bi-exclamation-octagon-fill';
                                } elseif ($lowStock) {
                                    $bg  = 'background:#fffbeb;';
                                    $clr = 'color:#d97706;';
                                    $ico = 'bi-exclamation-triangle-fill';
                                } else {
                                    $bg  = 'background:#f0fdf4;';
                                    $clr = 'color:#16a34a;';
                                    $ico = 'bi-check-circle-fill';
                                }
                            @endphp
                            <a href="{{ route('products.show', $product->id) }}"
                               class="notif-row d-flex align-items-start gap-3 px-3 py-3 text-decoration-none"
                               style="border-bottom:1px solid #f1f5f9; color:inherit;">

                                <div class="flex-shrink-0 d-flex align-items-center justify-content-center mt-1"
                                     style="width:34px; height:34px; border-radius:8px; {{ $bg }} {{ $clr }}">
                                    <i class="bi {{ $ico }}" style="font-size:0.95rem;"></i>
                                </div>

                                <div class="flex-grow-1" style="min-width:0;">
                                    <div class="font-weight-bold text-slate-900 text-truncate"
                                         style="font-size:0.83rem; line-height:1.3;">{{ $product->product_name }}</div>
                                    <div class="text-slate-500 mt-1" style="font-size:0.75rem;">
                                        <i class="bi bi-upc-scan me-1"></i>{{ $product->product_code }}
                                    </div>
                                </div>

                                <div class="flex-shrink-0 text-end">
                                    <span class="d-block font-weight-bold" style="font-size:1rem; line-height:1; {{ $clr }}">
                                        {{ $qty }}
                                    </span>
                                    <span class="text-slate-400" style="font-size:0.7rem;">{{ $product->product_unit }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="d-flex flex-column align-items-center justify-content-center py-5 text-center px-4">
                                <div class="d-flex align-items-center justify-content-center mb-3"
                                     style="width:52px; height:52px; border-radius:50%; background:#f0fdf4;">
                                    <i class="bi bi-check-circle-fill" style="color:#22c55e; font-size:1.5rem;"></i>
                                </div>
                                <div class="font-weight-bold text-slate-900 mb-1" style="font-size:0.88rem;">{{ __('app.all-good') }}</div>
                                <div class="text-slate-400" style="font-size:0.78rem;">{{ __('app.no-notifications') }}</div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Panel footer --}}
                    @if($low_quantity_products->count() > 0)
                        <div style="border-top:1px solid #e2e8f0; background:#f8fafc;">
                            <a href="{{ route('products.index') }}"
                               class="d-flex align-items-center justify-content-center gap-2 py-3 text-decoration-none font-weight-bold"
                               style="font-size:0.82rem; color:#4f46e5; transition:color .15s ease;"
                               onmouseover="this.style.color='#4338ca'" onmouseout="this.style.color='#4f46e5'">
                                {{ __('app.view-all-products') }}
                                <i class="bi bi-arrow-right" style="font-size:0.8rem;"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </li>
        @endcan

        <li class="c-header-nav-item dropdown">
            <a class="c-header-nav-link text-slate-900" data-toggle="dropdown" href="#" role="button"
               aria-haspopup="true" aria-expanded="false">
                <div class="c-avatar mr-2">
                    <img class="c-avatar rounded-circle border" src="{{ auth()->user()->getFirstMediaUrl('avatars') }}" alt="Profile Image" style="width:36px; height:36px; object-fit:cover;">
                </div>
                <div class="d-flex flex-column">
                    <span class="font-weight-bold text-slate-900" style="font-size:14px;">{{ auth()->user()->name }}</span>
                    <span class="text-slate-500" style="font-size:11px;">{{ __('app.online') }} <i class="bi bi-circle-fill text-success icon-fs-11px"></i></span>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-1 shadow-lg border-0 bg-white">
                <div class="dropdown-header border-bottom py-2 px-3 text-slate-500 bg-slate-50"><strong>{{ __('app.account') }}</strong></div>
                <a class="dropdown-item py-2 px-3 text-slate-800" href="{{ route('profile.edit') }}">
                    <i class="mfe-2 bi bi-person icon-fs-1-2rem text-indigo-600"></i> {{ __('app.profile') }}
                </a>
                <a id="logout-link" class="dropdown-item py-2 px-3 text-slate-800" href="#">
                    <i class="mfe-2 bi bi-box-arrow-left icon-fs-1-2rem text-danger"></i> {{ __('app.logout') }}   
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</div>
