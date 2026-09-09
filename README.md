<picture>
    <source srcset="public/images/logo.png"  
            media="(prefers-color-scheme: dark)">
    <img src="public/images/logo-dark.png" alt="App Logo">
</picture>

> **Important Note:** This Project is ready for Production. But use code from main branch only. If you find any bug or have any suggestion please create an Issue.

RizqEngine is a web-based Point of Sale and inventory management system built with Laravel and Livewire.

# Tech Stack

- **Backend:** Laravel 13 (PHP 8.2+)
- **Frontend:** Livewire 4, Tailwind CSS 4, Vite
- **Database:** MySQL / MariaDB
- **PDF Generation:** Laravel Snappy

# Requirements

- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL or MariaDB

# Local Installation

- run `` git clone https://github.com/Mohammadtrabelsi/RizqEngine.git ``
- run ``composer install `` 
- run `` npm install ``
- run ``npm run dev``
- copy .env.example to .env
- run `` php artisan key:generate ``
- set up your database in the .env
- run `` php artisan migrate --seed ``
- run `` php artisan storage:link ``
- run `` php artisan serve ``
- then visit `` http://localhost:8000 or http://127.0.0.1:8000 ``.

> **Important Note:** "RizqEngine" uses Laravel Snappy Package for PDFs. If you are using Linux then no configuration is needed. But in other Operating Systems please refer to [Laravel Snappy Documentation](https://github.com/barryvdh/laravel-snappy).

# Docker Installation

This will start the application along with the mysql database using docker compose. Note that the `DB_HOST` variable must be the mysql docker container name, in this case `db`.

- run `` docker build -t rizqengine . `` 
- run `` docker compose up ``
- then visit `` http://localhost:8000 or http://127.0.0.1:8000 ``.

# Login Credentials

A user is seeded for every role. All accounts use the password `12345678`.

| Role        | Email                   | Password   |
|-------------|-------------------------|------------|
| Super Admin | super.admin@test.com    | 12345678   |
| Admin       | admin@test.com          | 12345678   |
| Owner       | owner@test.com          | 12345678   |
| Manager     | manager@test.com        | 12345678   |
| Cashier     | cashier@test.com        | 12345678   |

## Demo
![RizqEngine](public/images/screenshot.jpg)
**Live Demo:** will update soon

## RizqEngine Features

**Catalog & inventory**
- Products management with multiple images, categories and barcode printing
- Stock adjustments, multi-warehouse stock and inter-warehouse transfers
- Serial-number and batch/expiry tracking

**Sales & purchasing**
- Point of Sale (POS) with cart, discounts and receipts
- Quotations (print / email) and one-click conversion to sales or orders
- Purchases, plus sale and purchase returns with payments
- Orders workflow: customer orders, purchase orders (bon de commande) and delivery notes (bon de livraison)

**Finance**
- Expenses with categories, taxes and multiple currencies
- Cash register sessions (open / close / reconcile)
- Monthly budgets, cash outings and an invoice archive

**Contacts & logistics**
- Customer and supplier management with running balances and bulk import
- Fleet: drivers and vehicles

**Administration & insights**
- User management with roles & permissions
- Activity logs / audit trail (records who created, updated or deleted each record)
- Reports (Profit & Loss, Payments, Sales, Purchases, returns, inventory valuation, stock/product movement and more)
- System settings, units and currency configuration

# Documentation

A built-in user guide is available inside the app at `/documentation`, split into a page per topic and translated into English, French and Arabic. It walks through every module above, from getting started to settings.

# License
**[Creative Commons Attribution 4.0	cc-by-4.0](https://creativecommons.org/licenses/by/4.0/)**
