<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>{{ config('app.name') }} &mdash; Modern Point of Sale &amp; Inventory</title>
    <meta name="description"
          content="Triangle POS is a complete Point of Sale and inventory management system: products & barcodes, stock, purchases, sales, returns, expenses, people, reports and role-based user management.">

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('images/favicon.png') }}">

    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <!-- Application CSS -->
    @vite('resources/css/app.css')
</head>
<body class="lp bg-slate-50 text-slate-900">

    @php
        $triangle = function ($size = 24, $stroke = 8) {
            $h = round($size * 90 / 100);
            return '<svg width="'.$size.'" height="'.$h.'" viewBox="0 0 100 90" aria-hidden="true" style="display:inline-block; vertical-align:middle;">'
                .'<polygon points="50,5 27.5,47.5 72.5,47.5" fill="none" stroke="#0f172a" stroke-width="'.$stroke.'" stroke-linejoin="round"></polygon>'
                .'<polygon points="5,90 27.5,47.5 50,90" fill="none" stroke="#0f172a" stroke-width="'.$stroke.'" stroke-linejoin="round"></polygon>'
                .'<polygon points="95,90 72.5,47.5 50,90" fill="none" stroke="#0f172a" stroke-width="'.$stroke.'" stroke-linejoin="round"></polygon>'
                .'<polygon points="27.5,47.5 72.5,47.5 50,90" fill="#4f46e5" stroke="#4f46e5" stroke-width="'.$stroke.'" stroke-linejoin="round"></polygon>'
                .'</svg>';
        };
    @endphp

    {{-- Navigation --}}
    <nav class="lp-nav bg-white border-bottom border-slate-200">
        <div class="lp-wrap lp-nav__inner">
            <a class="lp-brand font-weight-bold text-slate-900" href="{{ route('welcome') }}">
                {!! $triangle(22, 8) !!}
                Triangle POS
            </a>
            <div class="lp-nav__links">
                <a href="#features">Features</a>
                <a href="#access">Access control</a>
                <a href="#reporting">{{ __('general.reporting') }}</a>
                @include('includes.language-switcher')
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm ms-2">{{ __('login.sign-in') }}</a>
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <header class="lp-wrap lp-hero">
        <div class="lp-hero__copy">
            <span class="lp-kicker text-indigo-600 font-weight-bold">Point of Sale &amp; Inventory Platform</span>
            <h1 class="lp-title text-slate-900">Run your store with <span style="color:#4f46e5;">Triangle POS</span></h1>
            <p class="lp-lead text-slate-600">A complete, production-ready POS and inventory platform. Manage products, stock, purchases, sales, returns, expenses and staff — with real-time reporting and fine-grained access control.</p>
            <div class="lp-hero__actions">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg shadow-sm">{{ __('general.get-started') }}</a>
                <a href="#features" class="btn btn-secondary btn-lg">{{ __('general.see-features') ?? 'See features' }}</a>
            </div>
        </div>
        <div class="lp-hero__media">
            <div class="lp-shot bg-white border rounded-lg shadow-sm">
                <div class="d-flex flex-column align-items-center gap-2 p-4 text-center">
                    {!! $triangle(48, 6) !!}
                    <span class="fw-semibold text-indigo-600 fs-5 mt-2">Triangle POS Operations Control</span>
                    <span class="text-slate-500 small">Real-Time Till & Inventory Engine</span>
                </div>
            </div>
        </div>
    </header>

    {{-- Features --}}
    <section id="features" class="lp-wrap lp-section">
        <div class="lp-section__head">
            <h2 class="text-slate-900">Everything the shop floor needs</h2>
            <p class="text-slate-600">One unified system for every part of running a store, from stock room to checkout till.</p>
        </div>
        <div class="lp-features">
            @php
                $features = [
                    ['title' => 'Products & inventory', 'desc' => 'Track stock levels, variants, barcodes and categories across every store location.'],
                    ['title' => 'Purchases & suppliers', 'desc' => 'Manage supplier orders, incoming purchase batches and cost of goods seamlessly.'],
                    ['title' => 'Sales & Till POS', 'desc' => 'A lightning-fast checkout counter flow optimized for high-volume store hours.'],
                    ['title' => 'Returns & Refunds', 'desc' => 'Process customer returns, exchanges and store credit without breaking stock counts.'],
                    ['title' => 'Expense Tracking', 'desc' => 'Log day-to-day store expenses and automatically connect them to net income.'],
                    ['title' => 'People & Roles', 'desc' => 'Manage cashier, manager and owner accounts with granular permission sets.'],
                ];
            @endphp
            @foreach($features as $f)
                <div class="card lp-feature bg-white border rounded-lg shadow-sm">
                    <span class="lp-feature__icon rounded p-2" style="background:#eef2ff; width:fit-content;">{!! $triangle(22, 8) !!}</span>
                    <h3 class="text-slate-900 mt-2">{{ $f['title'] }}</h3>
                    <p class="text-slate-600 mb-0 small">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Role-based access band --}}
    <section id="access" class="lp-band py-5" style="background:#f1f5f9; border-top:1px solid #e2e8f0; border-bottom:1px solid #e2e8f0;">
        <div class="lp-wrap lp-band__inner">
            <div class="lp-band__copy">
                <h2 class="text-slate-900">{{ __('general.role-based-access') }}</h2>
                <p class="text-slate-600">{{ __('general.role-based-access-description') }}</p>
            </div>
            <div class="lp-roles d-flex flex-column gap-2">
                @php
                    $roles = [
                        ['name' => 'Owner', 'access' => 'Full administrative access & financial reporting'],
                        ['name' => 'Manager', 'access' => 'Full inventory, purchasing & sales management'],
                        ['name' => 'Cashier', 'access' => 'POS checkout till & returns processing only'],
                    ];
                @endphp
                @foreach($roles as $r)
                    <div class="lp-role bg-white border rounded-md p-3 d-flex justify-content-between align-items-center shadow-sm">
                        <span class="lp-role__name font-weight-bold text-slate-900">{{ $r['name'] }}</span>
                        <span class="lp-role__access text-indigo-600 small">{{ $r['access'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Reporting --}}
    <section id="reporting" class="lp-wrap lp-section">
        <div class="lp-split">
            <div class="lp-hero__media">
                <div class="lp-shot bg-white border rounded-lg shadow-sm">
                    <div class="d-flex flex-column align-items-center gap-2 p-4 text-center">
                        <i class="bi bi-graph-up-arrow fs-1" style="color:#4f46e5;"></i>
                        <span class="fw-semibold text-indigo-600 fs-5">Live Profit &amp; Loss Analytics</span>
                        <span class="text-slate-500 small">Instant Margin &amp; Revenue Reports</span>
                    </div>
                </div>
            </div>
            <div class="lp-split__copy">
                <h2 class="text-slate-900">{{ __('general.reporting') }}</h2>
                <p class="text-slate-600">{{ __('general.reporting-description') }}</p>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="lp-wrap lp-section" style="padding-top:0">
        <div class="lp-cta bg-white border rounded-lg p-4 p-md-5 shadow-sm">
            <div>
                <h2 class="text-slate-900">{{ __('general.ready-to-run-your-store') }}</h2>
                <p class="text-slate-600 mb-0 mt-1">Get started in seconds with modern point of sale management.</p>
            </div>
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg shadow-sm">{{ __('general.get-started') }}</a>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="lp-footer bg-white border-top border-slate-200">
        <div class="lp-wrap lp-footer__inner">
            <span class="text-slate-500 small">© {{ date('Y') }} Triangle POS</span>
            <div class="lp-footer__links">
                <a href="#" class="text-slate-600 small">{{ __('general.privacy') }}</a>
                <a href="#" class="text-slate-600 small ms-3">{{ __('general.terms') }}</a>
            </div>
        </div>
    </footer>

</body>
</html>
