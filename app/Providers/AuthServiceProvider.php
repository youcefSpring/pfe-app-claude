<?php

namespace App\Providers;

use App\Models\Defense;
use App\Models\Project;
use App\Models\StudentGrade;
use App\Models\Team;
use App\Policies\DefensePolicy;
use App\Policies\ProjectPolicy;
use App\Policies\StudentGradePolicy;
use App\Policies\TeamPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Defense::class => DefensePolicy::class,
        Project::class => ProjectPolicy::class,
        StudentGrade::class => StudentGradePolicy::class,
        Team::class => TeamPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}