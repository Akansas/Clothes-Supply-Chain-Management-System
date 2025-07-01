<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // Admin users have access to all dashboards
        if ($user->role && $user->role->name === 'admin') {
            return $next($request);
        }

        // Check if user has any of the required roles
        if (!$user->role || !in_array($user->role->name, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
