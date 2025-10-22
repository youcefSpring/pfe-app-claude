<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Bootstrap Laravel
$app = Application::configure(basePath: __DIR__)
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        commands: __DIR__.'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Use models
use App\Models\Team;
use App\Models\User;
use App\Models\TeamMember;

echo "=== User Team Membership Debug ===\n\n";

// Get all users with student role
$students = User::where('role', 'student')->get();

echo "Total students found: " . $students->count() . "\n\n";

foreach ($students as $student) {
    echo "=== Student: " . $student->name . " (ID: " . $student->id . ") ===\n";

    // Check team membership
    $teamMember = $student->teamMember;
    echo "1. Has teamMember relationship: " . ($teamMember ? '✅ YES' : '❌ NO') . "\n";

    if ($teamMember) {
        echo "   - Team ID: " . $teamMember->team_id . "\n";
        echo "   - Role: " . $teamMember->role . "\n";

        $team = $teamMember->team;
        echo "   - Team Name: " . ($team ? $team->name : 'NOT FOUND') . "\n";

        if ($team) {
            echo "   - Team Status: " . $team->status . "\n";
            echo "   - Team Members Count: " . $team->members()->count() . "\n";

            // Check the exact condition from the blade
            $canManageFromTeam = $team->canManagePreferences();
            $hasTeamMember = !is_null($student->teamMember);
            $isInCorrectTeam = $student->teamMember && $student->teamMember->team_id === $team->id;

            echo "2. Team canManagePreferences(): " . ($canManageFromTeam ? '✅ YES' : '❌ NO') . "\n";
            echo "3. User has teamMember: " . ($hasTeamMember ? '✅ YES' : '❌ NO') . "\n";
            echo "4. User in correct team: " . ($isInCorrectTeam ? '✅ YES' : '❌ NO') . "\n";

            $finalCanManage = $canManageFromTeam && $hasTeamMember && $isInCorrectTeam;
            echo "5. FINAL RESULT - Can Manage: " . ($finalCanManage ? '✅ YES' : '❌ NO') . "\n";
        }
    } else {
        echo "   - Student is not in any team\n";
    }

    echo "\n" . str_repeat("-", 60) . "\n\n";
}

echo "=== Next Steps ===\n";
echo "If a student shows 'NO' for any condition:\n";
echo "1. Check if student has a TeamMember record\n";
echo "2. Verify the team_id matches in both User->teamMember and Team records\n";
echo "3. Ensure the team meets all canManagePreferences() conditions\n";