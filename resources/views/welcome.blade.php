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
    <link rel="stylesheet" href="{{ asset('css/nocturne.css') }}">
    <style>
        .lp-wrap { max-width: 1280px; margin: 0 auto; }
        .lp-hero { display: flex; align-items: center; gap: var(--space-8); padding: 96px var(--space-8); }
        .lp-hero > div { flex: 1; }
        .lp-features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-4); }
        .lp-access { display: flex; align-items: center; gap: var(--space-8); }
        .lp-access > div { flex: 1; }
        .lp-reporting { display: flex; align-items: center; gap: var(--space-8); padding: 80px var(--space-8); }
        .lp-reporting > div { flex: 1; }
        .lp-cta { display: flex; align-items: center; justify-content: space-between; gap: var(--space-6);
            padding: 64px var(--space-8); border-top: 1px solid var(--color-divider); }
        .lp-footer { display: flex; justify-content: space-between; gap: var(--space-4);
            padding: var(--space-6) var(--space-8); border-top: 1px solid var(--color-divider); }
        .lp-shot { aspect-ratio: 16/11; border-radius: var(--radius-lg);
            background: repeating-linear-gradient(135deg, var(--color-surface) 0px, var(--color-surface) 10px,
                var(--color-neutral-900) 10px, var(--color-neutral-900) 20px);
            display: flex; align-items: center; justify-content: center;
            font-family: monospace; font-size: 12px; color: var(--color-neutral-500); }
        @media (max-width: 860px) {
            .lp-hero, .lp-access, .lp-reporting, .lp-cta { flex-direction: column; align-items: flex-start; }
            .lp-features-grid { grid-template-columns: 1fr; }
            .lp-hero { padding: 56px var(--space-6); }
        }
    </style>
</head>
<body style="background:var(--color-bg); color:var(--color-text)">

    {{-- Nav --}}
    <div class="nav" style="padding:var(--space-4) var(--space-8); border-bottom:1px solid var(--color-divider)">
        <div class="nav-brand" style="display:flex; align-items:center; gap:var(--space-2); font-size:15px">
            <svg width="22" height="20" viewBox="0 0 100 90">
                <polygon points="50,5 27.5,47.5 72.5,47.5" fill="none" stroke="var(--color-text)" stroke-width="8"></polygon>
                <polygon points="5,90 27.5,47.5 50,90" fill="none" stroke="var(--color-text)" stroke-width="8"></polygon>
                <polygon points="95,90 72.5,47.5 50,90" fill="none" stroke="var(--color-text)" stroke-width="8"></polygon>
                <polygon points="27.5,47.5 72.5,47.5 50,90" fill="var(--color-accent)" stroke="var(--color-accent)" stroke-width="8"></polygon>
            </svg>
            Triangle POS
        </div>
        <a href="#features">Features</a>
        <a href="#access">Access control</a>
        <a href="#reporting">Reporting</a>
        @include('includes.language-switcher')
        <a href="{{ route('login') }}" class="btn btn-secondary">Sign in</a>
    </div>

    {{-- Hero --}}
    <div class="lp-wrap">
        <div class="lp-hero">
            <div style="display:flex; flex-direction:column; gap:var(--space-4)">
                <h1 style="max-width:560px">Run your store with Triangle POS</h1>
                <p class="text-muted" style="font-size:16px; max-width:480px; margin:0">A complete, production-ready POS and inventory platform. Manage products, stock, purchases, sales, returns, expenses and people — with powerful reporting and fine-grained role-based access control.</p>
                <div style="display:flex; gap:var(--space-3); margin-top:var(--space-3); flex-wrap:wrap">
                    <a href="{{ route('login') }}" class="btn btn-primary">Get started</a>
                    <a href="#features" class="btn btn-secondary">See features</a>
                </div>
            </div>
            <div>
                <div class="lp-shot">product screenshot</div>
            </div>
        </div>
    </div>

    {{-- Features --}}
    <div id="features" class="lp-wrap" style="padding:80px var(--space-8)">
        <h2 style="margin-bottom:var(--space-2)">Everything the shop floor needs</h2>
        <p class="text-muted" style="max-width:520px; margin-bottom:var(--space-6)">One system for every part of running a store, from stock room to till.</p>
        <div class="lp-features-grid">
            @php
                $features = [
                    ['title' => 'Products & inventory', 'desc' => 'Track stock levels, variants and categories across every location.'],
                    ['title' => 'Purchases', 'desc' => 'Manage suppliers and purchase orders as stock comes in.'],
                    ['title' => 'Sales', 'desc' => 'A fast checkout flow for the till, built for busy hours.'],
                    ['title' => 'Returns', 'desc' => 'Process refunds and exchanges without breaking your stock counts.'],
                    ['title' => 'Expenses', 'desc' => 'Log day-to-day spending and keep it tied to your reporting.'],
                    ['title' => 'People', 'desc' => 'Manage staff accounts and what each one can access.'],
                    ['title' => 'Reporting', 'desc' => 'Sales, margin and stock reports whenever you need them.'],
                ];
            @endphp
            @foreach($features as $f)
                <div class="card elev-sm">
                    <svg width="26" height="23" viewBox="0 0 100 90">
                        <polygon points="50,5 27.5,47.5 72.5,47.5" fill="none" stroke="var(--color-text)" stroke-width="9"></polygon>
                        <polygon points="5,90 27.5,47.5 50,90" fill="none" stroke="var(--color-text)" stroke-width="9"></polygon>
                        <polygon points="95,90 72.5,47.5 50,90" fill="none" stroke="var(--color-text)" stroke-width="9"></polygon>
                        <polygon points="27.5,47.5 72.5,47.5 50,90" fill="var(--color-accent)" stroke="var(--color-accent)" stroke-width="9"></polygon>
                    </svg>
                    <div class="card-title">{{ $f['title'] }}</div>
                    <p class="card-body">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Role-based access band --}}
    <div id="access" style="padding:80px var(--space-8); background:var(--color-section); background-image:radial-gradient(ellipse at 20% 20%, var(--color-section-glow), transparent 60%)">
        <div class="lp-wrap lp-access">
            <div style="display:flex; flex-direction:column; gap:var(--space-3)">
                <h2 style="color:var(--color-neutral-100)">{{ __('general.role-based-access') }}</h2>
                <p style="color:var(--color-neutral-300); max-width:440px; margin:0">{{ __('general.role-based-access-description') }}</p>
            </div>
            <div style="display:flex; flex-direction:column; gap:var(--space-3); width:100%">
                @php
                    $roles = [
                        ['name' => 'Owner', 'access' => 'Full access'],
                        ['name' => 'Manager', 'access' => 'All except settings'],
                        ['name' => 'Cashier', 'access' => 'Sales & returns only'],
                    ];
                @endphp
                @foreach($roles as $r)
                    <div style="display:flex; align-items:center; justify-content:space-between; background:var(--color-section-ghost); border-radius:var(--radius-md); padding:var(--space-4)">
                        <div style="font-weight:500; font-size:14px; color:var(--color-neutral-100)">{{ $r['name'] }}</div>
                        <div style="font-size:13px; color:var(--color-neutral-300)">{{ $r['access'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Reporting --}}
    <div id="reporting" class="lp-wrap lp-reporting">
        <div>
            <div class="lp-shot">{{ __('general.reporting-dashboard-screenshot') }}</div>
        </div>
        <div style="display:flex; flex-direction:column; gap:var(--space-3)">
            <h2>{{ __('general.reporting') }}</h2>
            <p class="text-muted" style="max-width:440px; margin:0">{{ __('general.reporting-description') }}</p>
        </div>
    </div>

    {{-- CTA --}}
    <div class="lp-wrap lp-cta">
        <h3 style="margin:0">{{ __('general.ready-to-run-your-store') }}</h3>
        <a href="{{ route('login') }}" class="btn btn-primary">{{ __('general.get-started') }}</a>
    </div>

    {{-- Footer --}}
    <div class="lp-wrap lp-footer">
        <div class="text-muted" style="font-size:13px">© {{ date('Y') }} Triangle POS</div>
        <div style="display:flex; gap:var(--space-4)">
            <a href="#" style="font-size:13px">{{ __('general.privacy') }}</a>
            <a href="#" style="font-size:13px">{{ __('general.terms') }}</a>
        </div>
    </div>

</body>
</html>
