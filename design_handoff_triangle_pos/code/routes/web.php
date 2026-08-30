<?php

// routes/web.php (relevant entries)

use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\LandingPage;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPage::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});

// Locale switch used by the EN/FR selector
Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['en', 'fr'], true), 404);
    session(['locale' => $locale]);

    return back();
})->name('locale.switch');
