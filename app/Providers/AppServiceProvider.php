<?php

namespace App\Providers;

use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
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

        // Habilitations par profil métier (§14) — fail-closed :
        // une capacité absente de la matrice est refusée à tous.
        foreach (PermissionService::MATRICE as $capacite => $profils) {
            Gate::define($capacite, fn (User $utilisateur) => in_array($utilisateur->profile, $profils, true));
        }
    }
}
