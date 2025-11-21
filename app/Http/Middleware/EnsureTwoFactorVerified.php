<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class EnsureTwoFactorVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->hasRole('citizen')) {
            return $next($request);
        }

        if ($user->two_factor_secret && !session('two_factor_confirmed')) {
            return redirect()->route('two-factor-auth');
        }

        return $next($request);
    }

}
