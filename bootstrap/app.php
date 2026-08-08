<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Service providers, middleware, and routes are still registered the
| classic way (config/app.php's "providers" array and
| App\Providers\RouteServiceProvider), so this only needs to configure
| the pieces that used to live in app/Http/Kernel.php,
| app/Console/Kernel.php and app/Exceptions/Handler.php.
|
*/

return Application::configure(basePath: dirname(__DIR__))
    ->withCommands([
        base_path('routes/console.php'),
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
