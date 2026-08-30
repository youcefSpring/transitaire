<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        // Pagination rendue avec le style maison (.pagination) plutôt que le gabarit Tailwind par défaut
        Paginator::defaultView('vendor.pagination.transiatire');
        Paginator::defaultSimpleView('vendor.pagination.transiatire');
    }
}
