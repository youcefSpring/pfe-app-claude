<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SettingsService;

class CheckExternalProjectsAllowed
{
    /**
     * Handle an incoming request.
     *
     * Check if external projects are allowed in system settings before allowing access.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!SettingsService::areExternalProjectsAllowed()) {
            return redirect()
                ->route('dashboard')
                ->with('error', __('app.external_projects_disabled'));
        }

        return $next($request);
    }
}
