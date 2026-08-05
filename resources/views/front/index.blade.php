<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>{{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <!-- CoreUI CSS -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <style>
        .front-hero {
            background: linear-gradient(135deg, #2b2b39 0%, #321fdb 100%);
            color: #fff;
        }
        .front-hero .btn-light {
            font-weight: 600;
        }
        .front-feature-icon {
            width: 56px;
            height: 56px;
            border-radius: .5rem;
            background: rgba(50, 31, 219, .1);
            color: #321fdb;
            font-size: 1.5rem;
        }
        .front-screenshot {
            border-radius: .5rem;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .35);
            max-width: 100%;
        }
        .front-footer {
            background: #2b2b39;
            color: rgba(255, 255, 255, .7);
        }
    </style>
</head>

<body class="c-app">

<!-- Navbar -->
<nav class="navbar navbar-expand-md navbar-light bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('images/logo-dark.png') }}" alt="{{ config('app.name') }}" height="36">
        </a>
        <a href="{{ route('login') }}" class="btn btn-primary px-4">
            {{ __('auth.login') }}
        </a>
    </div>
</nav>

<!-- Hero -->
<header class="front-hero py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 font-weight-bold mb-3">{{ config('app.name') }}</h1>
                <p class="lead mb-4">
                    A complete point-of-sale &amp; inventory management system to run your sales, purchases, stock
                    and reporting from one place.
                </p>
                <a href="{{ route('login') }}" class="btn btn-light btn-lg px-4">
                    Login to Dashboard <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0 text-center">
                <img src="{{ asset('images/screenshot.jpg') }}" alt="{{ config('app.name') }} dashboard" class="front-screenshot">
            </div>
        </div>
    </div>
</header>

<!-- Features -->
<section class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="font-weight-bold">Everything you need to run your business</h2>
            <p class="text-muted">All the core modules that come built into {{ config('app.name') }}</p>
        </div>

        <div class="row">
            @foreach ([
                ['icon' => 'bi-receipt', 'title' => 'Point of Sale', 'desc' => 'Fast, intuitive checkout for processing sales and printing invoices.'],
                ['icon' => 'bi-journal-bookmark', 'title' => 'Product & Inventory', 'desc' => 'Manage products, categories, units and stock levels with ease.'],
                ['icon' => 'bi-bag', 'title' => 'Purchases', 'desc' => 'Record purchases from suppliers and keep stock automatically in sync.'],
                ['icon' => 'bi-arrow-return-left', 'title' => 'Sales & Purchase Returns', 'desc' => 'Handle customer and supplier returns without breaking your records.'],
                ['icon' => 'bi-clipboard-check', 'title' => 'Stock Adjustments', 'desc' => 'Correct inventory counts and track every adjustment made.'],
                ['icon' => 'bi-cart-check', 'title' => 'Quotations', 'desc' => 'Create and send quotations that convert into sales in one click.'],
                ['icon' => 'bi-wallet2', 'title' => 'Expense Tracking', 'desc' => 'Log and categorize business expenses to keep your books accurate.'],
                ['icon' => 'bi-people', 'title' => 'Customers & Suppliers', 'desc' => 'Keep a full directory of parties along with their transaction history.'],
                ['icon' => 'bi-graph-up', 'title' => 'Reports & Analytics', 'desc' => 'Profit & loss, payments, sales and purchase reports at a glance.'],
                ['icon' => 'bi-shield-lock', 'title' => 'User Management & Roles', 'desc' => 'Invite staff and control access with granular roles and permissions.'],
                ['icon' => 'bi-cash-stack', 'title' => 'Multi-Currency', 'desc' => 'Configure multiple currencies to sell and report across markets.'],
                ['icon' => 'bi-upc-scan', 'title' => 'Barcode Printing', 'desc' => 'Generate and print product barcodes directly from the system.'],
            ] as $feature)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="d-flex align-items-start">
                        <div class="front-feature-icon d-flex align-items-center justify-content-center mr-3 flex-shrink-0">
                            <i class="bi {{ $feature['icon'] }}"></i>
                        </div>
                        <div>
                            <h5 class="font-weight-bold mb-1">{{ $feature['title'] }}</h5>
                            <p class="text-muted mb-0">{{ $feature['desc'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA -->
<section class="bg-light py-5">
    <div class="container py-3 text-center">
        <h3 class="font-weight-bold mb-3">Ready to get started?</h3>
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5">
            {{ __('auth.login') }}
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="front-footer py-4">
    <div class="container text-center small">
        &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('auth.developed-by') }}
        <a href="https://fahimanzam.netlify.app" class="text-white font-weight-bold">Fahim Anzam Dip</a>
    </div>
</footer>

<script src="{{ mix('js/app.js') }}" defer></script>
</body>
</html>
