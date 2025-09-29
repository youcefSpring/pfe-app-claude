<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $userRole = $user->role;

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($userRole === $role) {
                return $next($request);
            }
        }

        // If no role matches, deny access
        abort(403, 'Access denied. Insufficient permissions.');
    }
}
