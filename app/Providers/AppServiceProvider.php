<?php

namespace App\Providers;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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

    /**
     * Daftarkan Authorization Gates berbasis ABAC dari Model User.
     */
    protected function registerAuthorizationGates(): void
    {
        // Gate untuk fitur khusus Admin
        Gate::define('admin-access', function (User $user) {
            return $user->isAdmin();
        });

        // Gate untuk fitur khusus Archivist / Sekretariat & Admin
        Gate::define('archivist-access', function (User $user) {
            return $user->canManageSekretariat();
        });

        // Gate untuk fitur Scanner QR & Presensi (Ranger / Archivist / Admin)
        Gate::define('scan-presensi', function (User $user) {
            return $user->canScanPresensi();
        });

        Gate::define('manage-kegiatan', function (User $user) {
            return $user->canManageKegiatan();
        });

        Gate::define('toggle-presensi', function (User $user) {
            return $user->canTogglePresensi();
        });

        // Gate untuk Akses Divisi Ranger
        Gate::define('ranger-access', function (User $user) {
            return $user->isAdmin() || $user->isRanger();
        });

        // Gate untuk Review Pengajuan Izin
        Gate::define('review-izin', function (User $user) {
            return $user->canReviewIzin();
        });

        // Gate untuk Melihat List/Monitoring Panitia
        Gate::define('view-panitia-list', function (User $user) {
            return $user->canViewPanitiaList();
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        App::setLocale('id');
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn (): ?Password => app()->isProduction()
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
