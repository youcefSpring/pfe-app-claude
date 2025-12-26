<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SettingsService;

class CheckPreferencesEnabled
{
    /**
     * Handle an incoming request.
     *
     * Check if subject preferences are enabled in system settings before allowing access.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!SettingsService::arePreferencesEnabled()) {
            return redirect()
                ->route('dashboard')
                ->with('error', __('app.preferences_disabled'));
        }

        return $next($request);
    }
}
