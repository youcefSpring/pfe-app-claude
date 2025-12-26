<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SettingsService;

class CheckRegistrationOpen
{
    /**
     * Handle an incoming request.
     *
     * Check if student registration is open in system settings before allowing access.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!SettingsService::isRegistrationOpen()) {
            return redirect()
                ->route('dashboard')
                ->with('error', __('app.registration_closed'));
        }

        return $next($request);
    }
}
