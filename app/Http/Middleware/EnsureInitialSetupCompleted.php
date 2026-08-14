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

        if ($user && ! $user->is_initial_setup_completed) {
            if (! $request->routeIs('initial-setup.*') && ! $request->routeIs('logout')) {
                return redirect()->route('initial-setup.index');
            }
        }

        return $next($request);
    }
}
