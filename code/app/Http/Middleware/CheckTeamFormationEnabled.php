<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SettingsService;

class CheckTeamFormationEnabled
{
    /**
     * Handle an incoming request.
     *
     * Check if team formation is enabled in system settings before allowing access.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!SettingsService::isTeamFormationEnabled()) {
            return redirect()
                ->route('dashboard')
                ->with('error', __('app.team_formation_disabled'));
        }

        return $next($request);
    }
}
