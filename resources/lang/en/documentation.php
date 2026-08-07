<?php

return [
    'documentation' => 'Documentation',
    'home' => 'Home',
    'page_title' => 'User Guide & Documentation',
    'intro' => 'Welcome to Triangle POS. This guide walks you through the main areas of the system so you can start managing products, sales, purchases and reports with confidence. Use the contents on the left to jump to a topic.',
    'toc' => 'Contents',
    'need_help' => 'Need more help?',
    'need_help_body' => 'If you cannot find what you are looking for, contact your system administrator or open an issue on the project repository.',

    'sections' => [
        'getting_started' => [
            'title' => 'Getting Started',
            'body' => [
                'Sign in with the email and password provided by your administrator. After logging in you land on the dashboard (Home).',
                'What you can see and do depends on your role. Triangle POS ships with the roles Super Admin, Admin, Owner, Manager and Cashier, each with its own set of permissions.',
                'Use the sidebar on the left to navigate between modules. Menu items you do not have permission for are hidden automatically.',
                'You can change the interface language and switch between light and dark themes from the top header bar.',
            ],
        ],
        'dashboard' => [
            'title' => 'Dashboard',
            'body' => [
                'The dashboard gives you a quick overview of your business: total sales, purchases, returns and profit.',
                'Charts show sales versus purchases over time, the current month activity and the payment flow so you can spot trends at a glance.',
            ],
        ],
        'products' => [
            'title' => 'Products & Barcodes',
            'body' => [
                'Open Products to create and manage your catalog. Each product can have a code, cost, price, unit, stock quantity and multiple images.',
                'Group products using Categories to keep the catalog organized and to filter faster on the POS screen.',
                'Use Print Barcode to generate and print barcode labels for your products, ready to scan at the point of sale.',
            ],
        ],
        'adjustments' => [
            'title' => 'Stock Adjustments',
            'body' => [
                'Stock Adjustments let you correct inventory quantities when stock is added or removed outside of a normal purchase or sale (for example damage, loss or a stock count).',
                'Create an adjustment, add the affected products and choose whether each line increases or decreases the quantity on hand.',
            ],
        ],
        'quotations' => [
            'title' => 'Quotations',
            'body' => [
                'Prepare price quotations for your customers before they commit to a purchase.',
                'A quotation can be printed or emailed directly to the customer, and later converted into a sale.',
            ],
        ],
        'purchases' => [
            'title' => 'Purchases & Purchase Returns',
            'body' => [
                'Record stock you buy from suppliers under Purchases. Adding a purchase increases product stock and lets you track the amount paid and due.',
                'Use Purchase Returns when you send goods back to a supplier. This reduces stock and adjusts the supplier balance accordingly.',
                'Payments can be recorded against each purchase or purchase return to keep supplier balances accurate.',
            ],
        ],
        'sales' => [
            'title' => 'Sales & Point of Sale',
            'body' => [
                'The Sales / POS screen is where you ring up customer orders. Search or scan products, add them to the cart, apply discounts or tax, then take payment and print the receipt.',
                'Every completed sale reduces stock automatically and updates the customer balance.',
                'Use Sale Returns to process refunds or exchanges. Returning items puts stock back and adjusts the customer balance.',
            ],
        ],
        'expenses' => [
            'title' => 'Expenses',
            'body' => [
                'Track business costs such as rent, utilities or salaries under Expenses.',
                'Organize expenses with Expense Categories so they can be filtered and reported on later.',
            ],
        ],
        'parties' => [
            'title' => 'Customers & Suppliers',
            'body' => [
                'Manage the people you do business with under Parties.',
                'Customers are used on sales and quotations, while Suppliers are used on purchases. Each party keeps a running balance of what is owed.',
            ],
        ],
        'reports' => [
            'title' => 'Reports',
            'body' => [
                'The Reports module summarizes your data so you can make informed decisions.',
                'Available reports include Profit & Loss, Payments, Sales, Purchases, Sales Return, Purchases Return, Inventory Valuation, Low Stock, Stock Movement and Product Movement.',
                'Most reports can be filtered by date range and exported for printing.',
            ],
        ],
        'users' => [
            'title' => 'User Management',
            'body' => [
                'Administrators can create users and assign them a role under User Management.',
                'Roles & Permissions control exactly which modules and actions each user can access, so you can give staff only what they need.',
            ],
        ],
        'activity_logs' => [
            'title' => 'Activity Logs',
            'body' => [
                'The Activity Log records who created, updated or deleted each record, providing a full audit trail.',
                'Use it to review changes and keep your team accountable.',
            ],
        ],
        'settings' => [
            'title' => 'Settings',
            'body' => [
                'System Settings hold your company details, default currency, notification email and mail (SMTP) configuration used for sending emails.',
                'Units define how products are measured and sold (for example piece, box or kilogram).',
                'Currencies let you configure the currencies and their symbols used across the system.',
            ],
        ],
    ],
];
