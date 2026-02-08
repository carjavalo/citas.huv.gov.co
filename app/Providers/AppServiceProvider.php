<?php

namespace App\Providers;

use Illuminate\pagination\paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

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
        paginator::useBootstrap();
        Schema::defaultStringLength(191);

        // Force HTTPS when APP_URL uses https or behind a proxy
        if (str_starts_with(config('app.url'), 'https') || 
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')) {
            URL::forceScheme('https');
        }
    }
}
