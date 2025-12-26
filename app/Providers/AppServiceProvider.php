<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share commonly-used settings with all views
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $view->with([
                'settingsMaintenanceMode' => \App\Services\SettingsService::isMaintenanceMode(),
                'settingsTeamFormationEnabled' => \App\Services\SettingsService::isTeamFormationEnabled(),
                'settingsPreferencesEnabled' => \App\Services\SettingsService::arePreferencesEnabled(),
                'settingsStudentsCanCreateSubjects' => \App\Services\SettingsService::canStudentsCreateSubjects(),
                'settingsExternalProjectsAllowed' => \App\Services\SettingsService::areExternalProjectsAllowed(),
                'settingsUniversityInfo' => \App\Services\SettingsService::getUniversityInfo(),
            ]);
        });
    }
}
