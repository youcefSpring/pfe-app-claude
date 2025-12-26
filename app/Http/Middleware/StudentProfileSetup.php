<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StudentProfileSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Only check for students
        if ($user && $user->role === 'student') {
            // Skip check if already on setup routes
            if ($request->routeIs('student.setup.*')) {
                return $next($request);
            }

            // Skip check for API routes, logout, etc.
            if ($request->routeIs('api.*') || $request->routeIs('logout')) {
                return $next($request);
            }

            // Check if student needs to complete profile setup
            if ($user->needsProfileSetup()) {
                return redirect()->route('student.setup.welcome');
            }
        }

        return $next($request);
    }
}
