<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\SettingsService;

class SettingsViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * Share settings flags with all views for conditional rendering
     */
    public function boot(): void
    {
        // Share settings flags with all views
        View::composer('*', function ($view) {
            $view->with('settings', [
                'teamFormationEnabled' => SettingsService::isTeamFormationEnabled(),
                'studentSubjectCreationEnabled' => SettingsService::canStudentsCreateSubjects(),
                'preferencesEnabled' => SettingsService::arePreferencesEnabled(),
                'registrationOpen' => SettingsService::isRegistrationOpen(),
                'externalProjectsAllowed' => SettingsService::areExternalProjectsAllowed(),
                'emailNotificationsEnabled' => SettingsService::areEmailNotificationsEnabled(),
                'autoSchedulingEnabled' => SettingsService::isAutoSchedulingEnabled(),
                'autoAllocationEnabled' => SettingsService::isAutoAllocationEnabled(),
            ]);
        });
    }
}
