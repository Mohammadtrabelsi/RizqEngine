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
    <!-- Application CSS (Tailwind) -->
    @vite('resources/css/app.css')
</head>
<body class="lp">

    @php
        $triangle = function ($size, $stroke = 8) {
            $h = round($size * 90 / 100);
            return '<svg width="'.$size.'" height="'.$h.'" viewBox="0 0 100 90" aria-hidden="true">'
                .'<polygon points="50,5 27.5,47.5 72.5,47.5" fill="none" stroke="currentColor" stroke-width="'.$stroke.'"></polygon>'
                .'<polygon points="5,90 27.5,47.5 50,90" fill="none" stroke="currentColor" stroke-width="'.$stroke.'"></polygon>'
                .'<polygon points="95,90 72.5,47.5 50,90" fill="none" stroke="currentColor" stroke-width="'.$stroke.'"></polygon>'
                .'<polygon points="27.5,47.5 72.5,47.5 50,90" fill="var(--primary)" stroke="var(--primary)" stroke-width="'.$stroke.'"></polygon>'
                .'</svg>';
        };
    @endphp

    {{-- Navigation --}}
    <nav class="lp-nav">
        <div class="lp-wrap lp-nav__inner">
            <a class="lp-brand" href="{{ route('welcome') }}">
                {!! $triangle(22) !!}
                Triangle POS
            </a>
            <div class="lp-nav__links">
                <a href="#features">Features</a>
                <a href="#access">Access control</a>
                <a href="#reporting">{{ __('general.reporting') }}</a>
                @include('includes.language-switcher')
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">{{ __('login.sign-in') }}</a>
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <header class="lp-wrap lp-hero">
        <div class="lp-hero__copy">
            <span class="lp-kicker">Point of sale &amp; inventory</span>
            <h1 class="lp-title">Run your store with <span class="lp-title__grad">Triangle POS</span></h1>
            <p class="lp-lead">A complete, production-ready POS and inventory platform. Manage products, stock, purchases, sales, returns, expenses and people — with powerful reporting and fine-grained role-based access control.</p>
            <div class="lp-hero__actions">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">{{ __('general.get-started') }}</a>
                <a href="#features" class="btn btn-secondary btn-lg">See features</a>
            </div>
        </div>
        <div class="lp-hero__media">
            <div class="lp-shot"><span>product screenshot</span></div>
        </div>
    </header>

    {{-- Features --}}
    <section id="features" class="lp-wrap lp-section">
        <div class="lp-section__head">
            <h2>Everything the shop floor needs</h2>
            <p>One system for every part of running a store, from stock room to till.</p>
        </div>
        <div class="lp-features">
            @php
                $features = [
                    ['title' => 'Products & inventory', 'desc' => 'Track stock levels, variants and categories across every location.'],
                    ['title' => 'Purchases', 'desc' => 'Manage suppliers and purchase orders as stock comes in.'],
                    ['title' => 'Sales', 'desc' => 'A fast checkout flow for the till, built for busy hours.'],
                    ['title' => 'Returns', 'desc' => 'Process refunds and exchanges without breaking your stock counts.'],
                    ['title' => 'Expenses', 'desc' => 'Log day-to-day spending and keep it tied to your reporting.'],
                    ['title' => 'People', 'desc' => 'Manage staff accounts and what each one can access.'],
                ];
            @endphp
            @foreach($features as $f)
                <div class="card lp-feature">
                    <span class="lp-feature__icon">{!! $triangle(24, 9) !!}</span>
                    <h3>{{ $f['title'] }}</h3>
                    <p>{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Role-based access band --}}
    <section id="access" class="lp-band">
        <div class="lp-wrap lp-band__inner">
            <div class="lp-band__copy">
                <h2>{{ __('general.role-based-access') }}</h2>
                <p>{{ __('general.role-based-access-description') }}</p>
            </div>
            <div class="lp-roles">
                @php
                    $roles = [
                        ['name' => 'Owner', 'access' => 'Full access'],
                        ['name' => 'Manager', 'access' => 'All except settings'],
                        ['name' => 'Cashier', 'access' => 'Sales & returns only'],
                    ];
                @endphp
                @foreach($roles as $r)
                    <div class="lp-role">
                        <span class="lp-role__name">{{ $r['name'] }}</span>
                        <span class="lp-role__access">{{ $r['access'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Reporting --}}
    <section id="reporting" class="lp-wrap lp-section">
        <div class="lp-split">
            <div class="lp-hero__media">
                <div class="lp-shot"><span>{{ __('general.reporting-dashboard-screenshot') }}</span></div>
            </div>
            <div class="lp-split__copy">
                <h2>{{ __('general.reporting') }}</h2>
                <p>{{ __('general.reporting-description') }}</p>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="lp-wrap lp-section" style="padding-top:0">
        <div class="lp-cta">
            <h2>{{ __('general.ready-to-run-your-store') }}</h2>
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">{{ __('general.get-started') }}</a>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="lp-footer">
        <div class="lp-wrap lp-footer__inner">
            <span class="text-muted">© {{ date('Y') }} Triangle POS</span>
            <div class="lp-footer__links">
                <a href="#">{{ __('general.privacy') }}</a>
                <a href="#">{{ __('general.terms') }}</a>
            </div>
        </div>
    </footer>

</body>
</html>
