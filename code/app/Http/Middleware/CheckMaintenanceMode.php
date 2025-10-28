<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SettingsService;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * Check if system is in maintenance mode. Only admins can access during maintenance.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if maintenance mode is enabled
        if (SettingsService::isMaintenanceMode()) {
            // Allow admins to access
            if ($request->user() && $request->user()->role === 'admin') {
                return $next($request);
            }

            // For non-admins, show maintenance page
            $message = SettingsService::getMaintenanceMessage();

            // If it's an AJAX request, return JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'maintenance' => true
                ], 503);
            }

            // Return maintenance view
            return response()->view('errors.maintenance', [
                'message' => $message
            ], 503);
        }

        return $next($request);
    }
}
