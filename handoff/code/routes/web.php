<?php

use App\Livewire\Dashboard;
use App\Livewire\Landing;
use App\Livewire\SignIn;
use Illuminate\Support\Facades\Route;

/*
| Triangle POS — public + app routes.
| Locale is resolved from the {locale} prefix (en|fr). The redirect sends the
| bare root to the visitor's preferred locale, defaulting to English.
*/

Route::redirect('/', '/en');

Route::prefix('{locale}')
    ->whereIn('locale', ['en', 'fr'])
    ->middleware('set-locale') // publish a SetLocale middleware that calls app()->setLocale()
    ->group(function () {
        Route::get('/', Landing::class)->name('landing');
        Route::get('/sign-in', SignIn::class)->name('sign-in');

        // Everything behind auth lives under the dashboard shell.
        Route::middleware('auth')->group(function () {
            Route::get('/dashboard', Dashboard::class)->name('dashboard');
            // Add: /pos, /products, /transactions, /quotes, /orders, /expenses,
            //      /customers, /suppliers, /staff, /reports, /settings
        });
    });
