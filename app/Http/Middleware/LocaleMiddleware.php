<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get available locales from config
        $availableLocales = array_keys(config('app.available_locales', ['en']));

        // Get locale from session, user preference, or default
        $sessionLocale = Session::get('locale');
        $userLocale = auth()->check() ? auth()->user()->locale ?? null : null;
        $defaultLocale = config('app.locale', 'en');

        // Priority: session locale first (for language switching), then user preference, then default
        $preferredLocale = $sessionLocale ?: $userLocale ?: $defaultLocale;

        // Ensure the preferred locale is available
        if (!in_array($preferredLocale, $availableLocales)) {
            $preferredLocale = $defaultLocale;
        }

        App::setLocale($preferredLocale);

        return $next($request);
    }
}
