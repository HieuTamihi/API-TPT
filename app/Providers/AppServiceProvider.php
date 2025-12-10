<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
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
        // Ép toàn bộ link sinh ra phải là HTTPS
        if ($this->app->environment('production') || true) {
            URL::forceScheme('https');
        }
        // Ensure Bootstrap pagination view and Vietnamese labels
        if (config('app.env') === 'local') {
            URL::forceScheme('http');
        }
        Paginator::useBootstrap();
    }
}
