<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Jika web diakses lewat alamat yang mengandung kata 'ngrok'
        if (str_contains(request()->getHttpHost(), 'ngrok-free.dev') || str_contains(request()->getHttpHost(), 'ngrok.io')) {
            URL::forceScheme('https');
        }
    }
}
