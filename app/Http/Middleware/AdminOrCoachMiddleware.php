<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminOrCoachMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || (!$user->isAdmin() && !$user->isCoach())) {
            abort(403);
        }

        return $next($request);
    }
}
