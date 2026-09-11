<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use App\View\Composers\NavigationComposer;
use App\View\Composers\WelcomeComposer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::preventLazyLoading(! app()->isProduction());

        $this->definePasswordPolicy();

        Product::observe(ProductObserver::class);

        // Provide low-stock products to the header notifications dropdown so the
        // query lives here instead of an inline @php block in the view.
        View::composer('layouts.header', function ($view) {
            $view->with(
                'low_quantity_products',
                Product::select('id', 'product_quantity', 'product_stock_alert', 'product_code')
                    ->whereColumn('product_quantity', '<=', 'product_stock_alert')
                    ->get()
            );
        });

        // Active-section flags and store settings for the navigation, so the
        // sidebar and secondary menu carry no @php routing logic.
        View::composer(['layouts.sidebar', 'layouts.menu-secondary'], NavigationComposer::class);

        // Static marketing content for the landing page.
        View::composer('welcome', WelcomeComposer::class);
    }

    /**
     * Central password policy used everywhere a password is set (user
     * management, registration, profile). Kept in one place so the rules can
     * be tightened without hunting through controllers. Production enforces
     * complexity and, where the network allows, a breached-password check;
     * local/testing stays permissive so seeders and factories are unaffected.
     */
    private function definePasswordPolicy(): void
    {
        Password::defaults(function () {
            $rule = Password::min(8);

            if (app()->isProduction()) {
                return $rule->mixedCase()->numbers()->uncompromised();
            }

            return $rule;
        });
    }
}
