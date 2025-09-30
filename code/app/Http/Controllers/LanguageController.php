<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        // Get available locales from config
        $availableLocales = array_keys(config('app.available_locales', ['en']));

        // Validate the locale
        if (!in_array($locale, $availableLocales)) {
            return redirect()->back()->with('error', 'Language not supported.');
        }

        // Set the locale in session
        Session::put('locale', $locale);
        App::setLocale($locale);

        // If user is authenticated, update their locale preference
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        return redirect()->back()->with('success', __('app.language') . ' ' . __('app.updated'));
    }

    /**
     * Get current language information
     */
    public function current(): array
    {
        $currentLocale = App::getLocale();
        $availableLocales = config('app.available_locales', []);

        return [
            'current' => $currentLocale,
            'current_info' => $availableLocales[$currentLocale] ?? null,
            'available' => $availableLocales,
        ];
    }
}
