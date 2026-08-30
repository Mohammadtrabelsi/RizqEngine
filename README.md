<picture>
    <source srcset="public/images/logo.png"  
            media="(prefers-color-scheme: dark)">
    <img src="public/images/logo-dark.png" alt="App Logo">
</picture>

> **Important Note:** This Project is ready for Production. But use code from main branch only. If you find any bug or have any suggestion please create an Issue.

Triangle POS is a web-based Point of Sale and inventory management system built with Laravel and Livewire.

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

- run `` git clone https://github.com/Mohammadtrabelsi/triangle-pos.git ``
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

> **Important Note:** "Triangle POS" uses Laravel Snappy Package for PDFs. If you are using Linux then no configuration is needed. But in other Operating Systems please refer to [Laravel Snappy Documentation](https://github.com/barryvdh/laravel-snappy).

# Docker Installation

This will start the application along with the mysql database using docker compose. Note that the `DB_HOST` variable must be the mysql docker container name, in this case `db`.

- run `` docker build -t triangle-pos . `` 
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
![Triangle POS](public/images/screenshot.jpg)
**Live Demo:** will update soon

## Triangle POS Features

- **Products Management & Barcode Printing**
- **Stock Management**
- **Make Quotation & Send Via Email**
- **Purchase Management**
- **Sale Management**
- **Purchase & Sale Return Management**
- **Expense Management**
- **Customer & Supplier Management**
- **User Management (Roles & Permissions)**
- **Activity Logs / Audit Trail (records who created, updated or deleted each record)**
- **Product Multiple Images**
- **Multiple Currency Settings**
- **Unit Settings**
- **System Settings**
- **Reports**

# License
**[Creative Commons Attribution 4.0	cc-by-4.0](https://creativecommons.org/licenses/by/4.0/)**
