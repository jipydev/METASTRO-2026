<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInitialSetupCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // Kasus 1: User belum selesai setup, tapi mencoba akses halaman lain
            if (! $user->is_initial_setup_completed) {
                if (! $request->routeIs('initial-setup.*') && ! $request->routeIs('logout')) {
                    return redirect()->route('initial-setup.index');
                }
            }

            // Kasus 2: User SUDAH selesai setup, tapi mencoba akses kembali halaman onboarding
            if ($user->is_initial_setup_completed && $request->routeIs('initial-setup.*')) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
