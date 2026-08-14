<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        $this->configureDefaults();
        $this->registerAuthorizationGates();
    }

    protected function registerAuthorizationGates(): void
    {
        \Illuminate\Support\Facades\Gate::define('archivist-access', function (\App\Models\User $user) {
            return $user->canManageArchivistFeatures();
        });

        \Illuminate\Support\Facades\Gate::define('ranger-access', function (\App\Models\User $user) {
            return $user->canManageRangerFeatures();
        });

        \Illuminate\Support\Facades\Gate::define('view-panitia-list', function (\App\Models\User $user) {
            return $user->canViewPanitiaList();
        });

        \Illuminate\Support\Facades\Gate::define('review-izin', function (\App\Models\User $user) {
            return $user->isKetuaOrWakil() || $user->isRanger() || $user->isStakeholder() || $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('admin-access', function (\App\Models\User $user) {
            return $user->isAdmin();
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : Password::min(8),
        );
    }
}
