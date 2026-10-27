<?php

namespace App\Providers;

use Throwable;
use App\Models\Parametre;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;

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
        //


        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Dates affichées en français (translatedFormat, diffForHumans)
        \Carbon\Carbon::setLocale('fr');

        Schema::defaultStringLength(191);


        $this->app->booted(function () {
            // La synchro des permissions coûte ~10 requêtes SQL : on ne la lance
            // que pour l'administration (et en console), jamais sur le site public.
            if (! $this->app->runningInConsole() && ! request()->is('admin', 'admin/*')) {
                return;
            }
            // Pas pour les appels AJAX (notifications, calendrier…)
            if (! $this->app->runningInConsole() && (request()->ajax() || request()->expectsJson())) {
                return;
            }

            try {
                $permissions = Permission::pluck('id')->toArray();
                $roles = Role::whereIn('name', ['developpeur', 'superadmin'])->withCount('permissions')->get();

                // Ces rôles doivent avoir toutes les permissions : on ne
                // resynchronise que s'il en manque (2 requêtes au lieu de ~10)
                $synced = false;
                foreach ($roles as $role) {
                    if ($role->permissions_count !== count($permissions)) {
                        $role->permissions()->sync($permissions);
                        $synced = true;
                    }
                }
                if ($synced) {
                    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
                }
            } catch (Throwable $e) {
                report($e);
            }
        });



        // Récupérer les paramètres (try/catch plutôt que Schema::hasTable,
        // qui interroge information_schema à chaque requête)
        $data_parametre = null;
        try {
            $data_parametre = Parametre::with('media')->first();
        } catch (Throwable $e) {
            // Table absente (installation / migration en cours)
        }

        view()->share([
            'parametre' => $data_parametre ?? null,
        ]);
    }
}
