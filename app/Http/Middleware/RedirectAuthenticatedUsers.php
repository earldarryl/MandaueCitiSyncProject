<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAuthenticatedUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        if ($user->hasRole('admin') && !$request->routeIs('admin.dashboard')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('hr_liaison') && !$request->routeIs('hr-liaison.dashboard')) {
            return redirect()->route('hr-liaison.dashboard');
        }

        if ($user->hasRole('citizen') && !$request->routeIs('citizen.grievance.index')) {
            return redirect()->route('citizen.grievance.index');
        }

        return $next($request);
    }

}
