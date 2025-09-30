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

        // Check if locale is in URL
        $locale = $request->segment(1);

        if (in_array($locale, $availableLocales)) {
            // Set the locale from URL
            App::setLocale($locale);
            Session::put('locale', $locale);
        } else {
            // Get locale from session, user preference, or default
            $sessionLocale = Session::get('locale');
            $userLocale = auth()->check() ? auth()->user()->locale ?? null : null;
            $defaultLocale = config('app.locale', 'en');

            $preferredLocale = $sessionLocale ?: $userLocale ?: $defaultLocale;

            // Ensure the preferred locale is available
            if (!in_array($preferredLocale, $availableLocales)) {
                $preferredLocale = $defaultLocale;
            }

            App::setLocale($preferredLocale);
            Session::put('locale', $preferredLocale);
        }

        return $next($request);
    }
}
