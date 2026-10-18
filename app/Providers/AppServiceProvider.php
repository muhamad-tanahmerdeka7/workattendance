<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        Vite::prefetch(concurrency: 3);

        // Memberikan izin penuh (Super Admin bypass) kepada role HR Manager
        Gate::before(function ($user, $ability) {
            return $user->hasRole('HR Manager') ? true : null;
        });
    }
}
