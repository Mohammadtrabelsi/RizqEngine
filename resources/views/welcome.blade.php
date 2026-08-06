<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>{{ config('app.name') }} &mdash; Modern Point of Sale &amp; Inventory</title>
    <meta name="description"
          content="Triangle POS is a complete Point of Sale and inventory management system: products & barcodes, stock, purchases, sales, returns, expenses, people, reports and role-based user management.">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <style>
        :root {
            --tp-primary: #321fdb;
            --tp-primary-dark: #2419a8;
            --tp-dark: #12141c;
            --tp-muted: #5c6873;
            --tp-bg: #f5f6fb;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--tp-dark);
            background: var(--tp-bg);
            line-height: 1.6;
        }

        a { text-decoration: none; }

        .tp-container {
            width: 100%;
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Navbar */
        .tp-nav {
            position: sticky;
            top: 0;
            z-index: 30;
            background: rgba(255, 255, 255, .9);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid #e6e8f0;
        }
        .tp-nav .tp-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 68px;
        }
        .tp-nav img { height: 34px; }
        .tp-nav .tp-links { display: flex; align-items: center; gap: 26px; }
        .tp-nav .tp-links a { color: var(--tp-muted); font-weight: 500; }
        .tp-nav .tp-links a:hover { color: var(--tp-dark); }

        .btn-tp {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .btn-tp-primary {
            background: var(--tp-primary);
            color: #fff;
            box-shadow: 0 8px 20px rgba(50, 31, 219, .28);
        }
        .btn-tp-primary:hover { transform: translateY(-2px); color: #fff; box-shadow: 0 12px 26px rgba(50, 31, 219, .35); }
        .btn-tp-ghost { background: transparent; color: var(--tp-dark); border: 1px solid #d4d8e6; }
        .btn-tp-ghost:hover { border-color: var(--tp-primary); color: var(--tp-primary); }

        /* Hero */
        .tp-hero {
            padding: 84px 0 72px;
            background:
                radial-gradient(1200px 500px at 80% -10%, rgba(50, 31, 219, .10), transparent 60%),
                radial-gradient(900px 400px at 0% 20%, rgba(50, 31, 219, .06), transparent 55%);
        }
        .tp-hero .tp-grid {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 48px;
            align-items: center;
        }
        .tp-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(50, 31, 219, .1);
            color: var(--tp-primary);
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .tp-hero h1 {
            font-size: 46px;
            line-height: 1.12;
            font-weight: 800;
            margin: 0 0 18px;
            letter-spacing: -1px;
        }
        .tp-hero h1 span { color: var(--tp-primary); }
        .tp-hero p.lead { font-size: 19px; color: var(--tp-muted); margin: 0 0 30px; }
        .tp-cta { display: flex; gap: 14px; flex-wrap: wrap; }

        .tp-shot {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(18, 20, 28, .18);
            border: 1px solid #e6e8f0;
        }
        .tp-shot img { display: block; width: 100%; height: auto; }

        /* Stats */
        .tp-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 64px;
        }
        .tp-stat {
            background: #fff;
            border: 1px solid #e6e8f0;
            border-radius: 14px;
            padding: 22px;
            text-align: center;
        }
        .tp-stat .n { font-size: 30px; font-weight: 800; color: var(--tp-primary); }
        .tp-stat .l { color: var(--tp-muted); font-weight: 500; font-size: 15px; }

        /* Sections */
        .tp-section { padding: 78px 0; }
        .tp-section-head { text-align: center; max-width: 640px; margin: 0 auto 52px; }
        .tp-section-head h2 { font-size: 34px; font-weight: 800; margin: 0 0 12px; letter-spacing: -.5px; }
        .tp-section-head p { color: var(--tp-muted); font-size: 18px; margin: 0; }

        .tp-features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .tp-card {
            background: #fff;
            border: 1px solid #e6e8f0;
            border-radius: 16px;
            padding: 28px;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }
        .tp-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 36px rgba(18, 20, 28, .1);
            border-color: #d4d8e6;
        }
        .tp-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--tp-primary);
            background: rgba(50, 31, 219, .1);
            margin-bottom: 18px;
        }
        .tp-card h3 { font-size: 19px; font-weight: 700; margin: 0 0 8px; }
        .tp-card p { color: var(--tp-muted); margin: 0; font-size: 15px; }

        /* CTA band */
        .tp-band {
            background: linear-gradient(135deg, var(--tp-primary), var(--tp-primary-dark));
            border-radius: 22px;
            padding: 56px;
            text-align: center;
            color: #fff;
        }
        .tp-band h2 { font-size: 32px; font-weight: 800; margin: 0 0 12px; }
        .tp-band p { font-size: 18px; opacity: .9; margin: 0 0 26px; }
        .tp-band .btn-tp-primary { background: #fff; color: var(--tp-primary); box-shadow: none; }
        .tp-band .btn-tp-primary:hover { box-shadow: 0 12px 26px rgba(0, 0, 0, .2); color: var(--tp-primary); }

        .tp-note {
            margin-top: 18px;
            font-size: 14px;
            opacity: .85;
        }
        .tp-note code {
            background: rgba(255, 255, 255, .18);
            padding: 2px 7px;
            border-radius: 6px;
        }

        /* Footer */
        .tp-footer {
            padding: 32px 0;
            border-top: 1px solid #e6e8f0;
            text-align: center;
            color: var(--tp-muted);
        }
        .tp-footer a { color: var(--tp-primary); font-weight: 600; }

        @media (max-width: 900px) {
            .tp-hero .tp-grid { grid-template-columns: 1fr; }
            .tp-features { grid-template-columns: repeat(2, 1fr); }
            .tp-stats { grid-template-columns: repeat(2, 1fr); }
            .tp-nav .tp-links a:not(.btn-tp) { display: none; }
        }
        @media (max-width: 560px) {
            .tp-hero h1 { font-size: 34px; }
            .tp-features { grid-template-columns: 1fr; }
            .tp-band { padding: 36px 22px; }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <header class="tp-nav">
        <div class="tp-container tp-inner">
            <a href="{{ route('welcome') }}"><img src="{{ asset('images/logo-dark.png') }}" alt="{{ config('app.name') }}"></a>
            <nav class="tp-links">
                <a href="#features">Features</a>
                <a href="#modules">Modules</a>
                <a href="{{ route('login') }}" class="btn-tp btn-tp-primary">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
            </nav>
        </div>
    </header>

    <!-- Hero -->
    <section class="tp-hero">
        <div class="tp-container">
            <div class="tp-grid">
                <div>
                    <span class="tp-badge"><i class="bi bi-lightning-charge-fill"></i> Point of Sale &amp; Inventory</span>
                    <h1>Run your store with <span>Triangle POS</span></h1>
                    <p class="lead">
                        A complete, production-ready POS and inventory platform &mdash; manage products,
                        stock, purchases, sales, returns, expenses and people, with powerful reporting
                        and fine-grained role-based access control.
                    </p>
                    <div class="tp-cta">
                        <a href="{{ route('login') }}" class="btn-tp btn-tp-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Login to Dashboard
                        </a>
                        <a href="#features" class="btn-tp btn-tp-ghost">
                            <i class="bi bi-grid"></i> Explore Features
                        </a>
                    </div>
                </div>
                <div>
                    <div class="tp-shot">
                        <img src="{{ asset('images/screenshot.jpg') }}" alt="Triangle POS dashboard preview">
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="tp-stats">
                <div class="tp-stat"><div class="n">15+</div><div class="l">Modules</div></div>
                <div class="tp-stat"><div class="n">POS</div><div class="l">Fast Checkout</div></div>
                <div class="tp-stat"><div class="n">RBAC</div><div class="l">Roles &amp; Permissions</div></div>
                <div class="tp-stat"><div class="n">10+</div><div class="l">Reports</div></div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="tp-section" id="features">
        <div class="tp-container">
            <div class="tp-section-head">
                <h2>Everything you need to sell &amp; manage</h2>
                <p>Triangle POS bundles the day-to-day operations of a retail business into one clean, fast interface.</p>
            </div>

            <div class="tp-features">
                <div class="tp-card">
                    <div class="tp-icon"><i class="bi bi-upc-scan"></i></div>
                    <h3>Products &amp; Barcodes</h3>
                    <p>Manage products with categories, multiple images and units, then design and print barcode labels.</p>
                </div>
                <div class="tp-card">
                    <div class="tp-icon"><i class="bi bi-box-seam"></i></div>
                    <h3>Stock Management</h3>
                    <p>Track inventory in real time with stock adjustments and low-stock visibility across your catalog.</p>
                </div>
                <div class="tp-card">
                    <div class="tp-icon"><i class="bi bi-cart-check"></i></div>
                    <h3>Sales &amp; POS</h3>
                    <p>A quick point-of-sale checkout with cart, payments and receipts for smooth day-to-day selling.</p>
                </div>
                <div class="tp-card">
                    <div class="tp-icon"><i class="bi bi-truck"></i></div>
                    <h3>Purchase Management</h3>
                    <p>Record purchases from suppliers, manage costs and keep stock levels automatically in sync.</p>
                </div>
                <div class="tp-card">
                    <div class="tp-icon"><i class="bi bi-arrow-counterclockwise"></i></div>
                    <h3>Sale &amp; Purchase Returns</h3>
                    <p>Handle returns on both sales and purchases with full traceability and stock corrections.</p>
                </div>
                <div class="tp-card">
                    <div class="tp-icon"><i class="bi bi-file-earmark-text"></i></div>
                    <h3>Quotations</h3>
                    <p>Create quotations, email them to customers and convert accepted quotes directly into sales.</p>
                </div>
                <div class="tp-card">
                    <div class="tp-icon"><i class="bi bi-wallet2"></i></div>
                    <h3>Expense Management</h3>
                    <p>Log business expenses by category to keep an accurate picture of your cash flow.</p>
                </div>
                <div class="tp-card">
                    <div class="tp-icon"><i class="bi bi-people"></i></div>
                    <h3>Customers &amp; Suppliers</h3>
                    <p>Maintain your people directory of customers and suppliers with balances and history.</p>
                </div>
                <div class="tp-card">
                    <div class="tp-icon"><i class="bi bi-shield-lock"></i></div>
                    <h3>Users, Roles &amp; Permissions</h3>
                    <p>Fine-grained role-based access control, including a Super Admin role with full access.</p>
                </div>
                <div class="tp-card">
                    <div class="tp-icon"><i class="bi bi-clock-history"></i></div>
                    <h3>Activity Logs</h3>
                    <p>A complete audit trail recording who created, updated or deleted each record.</p>
                </div>
                <div class="tp-card">
                    <div class="tp-icon"><i class="bi bi-graph-up-arrow"></i></div>
                    <h3>Reports</h3>
                    <p>Sales, purchases, returns, profit &amp; loss, stock movement, inventory valuation and more.</p>
                </div>
                <div class="tp-card">
                    <div class="tp-icon"><i class="bi bi-gear"></i></div>
                    <h3>Settings &amp; Currency</h3>
                    <p>Configure system settings, units and multiple currencies to match how your business runs.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules -->
    <section class="tp-section" id="modules" style="background:#fff;border-top:1px solid #e6e8f0;border-bottom:1px solid #e6e8f0;">
        <div class="tp-container">
            <div class="tp-section-head">
                <h2>Built as modular building blocks</h2>
                <p>Each capability is a self-contained module, so the system stays organized and easy to extend.</p>
            </div>
            <div class="tp-features">
                @foreach ([
                    ['Product', 'bi-upc-scan'],
                    ['Adjustment', 'bi-sliders'],
                    ['Purchase', 'bi-truck'],
                    ['Sale', 'bi-cart-check'],
                    ['PurchasesReturn', 'bi-arrow-return-left'],
                    ['SalesReturn', 'bi-arrow-return-right'],
                    ['Quotation', 'bi-file-earmark-text'],
                    ['Expense', 'bi-wallet2'],
                    ['People', 'bi-people'],
                    ['Reports', 'bi-graph-up-arrow'],
                    ['User', 'bi-shield-lock'],
                    ['Currency', 'bi-cash-coin'],
                    ['Setting', 'bi-gear'],
                    ['Activitylog', 'bi-clock-history'],
                    ['Upload', 'bi-images'],
                ] as $module)
                    <div class="tp-card" style="display:flex;align-items:center;gap:14px;padding:18px 22px;">
                        <div class="tp-icon" style="margin:0;width:44px;height:44px;font-size:20px;">
                            <i class="bi {{ $module[1] }}"></i>
                        </div>
                        <h3 style="margin:0;font-size:17px;">{{ $module[0] }}</h3>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA band -->
    <section class="tp-section">
        <div class="tp-container">
            <div class="tp-band">
                <h2>Ready to jump in?</h2>
                <p>Sign in to your dashboard and start managing your store.</p>
                <a href="{{ route('login') }}" class="btn-tp btn-tp-primary">
                    <i class="bi bi-box-arrow-in-right"></i> Go to Login
                </a>
                <div class="tp-note">
                    Demo Super Admin &mdash; <code>super.admin@test.com</code> / <code>12345678</code>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="tp-footer">
        <div class="tp-container">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Developed by
            <a href="https://fahimanzam.netlify.app">Fahim Anzam Dip</a>.
        </div>
    </footer>
</body>
</html>
