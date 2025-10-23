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
use App\Models\Subject;

echo "=== Subject Preferences Availability Debug ===\n\n";

// Check subject_specialities table (same logic as controller)
$hasSpecialityRelationships = \DB::table('subject_specialities')->exists();
$specialityCount = \DB::table('subject_specialities')->count();

echo "1. Subject Specialities Table:\n";
echo "   - Table exists: " . ($hasSpecialityRelationships ? '✅ YES' : '❌ NO') . "\n";
echo "   - Record count: $specialityCount\n\n";

// Get team and check members' specialities
$team = Team::first();
if (!$team) {
    echo "❌ No teams found\n";
    exit;
}

echo "2. Team Analysis: " . $team->name . "\n";
$team->load(['members.user']);

echo "   Team Members:\n";
foreach ($team->members as $member) {
    $user = $member->user;
    echo "   - {$user->name} (ID: {$user->id})\n";
    echo "     Speciality ID: " . ($user->speciality_id ?? 'NULL') . "\n";
    echo "     Student Level: " . ($user->student_level ?? 'NULL') . "\n";
}

// Get team members' speciality IDs (same logic as controller)
$teamSpecialityIds = $team->members()
    ->with('user')
    ->get()
    ->pluck('user.speciality_id')
    ->filter()
    ->unique();

echo "\n3. Team Speciality IDs Collection:\n";
echo "   - Count: " . $teamSpecialityIds->count() . "\n";
echo "   - Values: [" . $teamSpecialityIds->implode(', ') . "]\n";
echo "   - Is Empty: " . ($teamSpecialityIds->isEmpty() ? '❌ YES' : '✅ NO') . "\n\n";

// Build subjects query (same logic as controller)
$subjectsQuery = Subject::where('status', 'validated')
    ->whereNotIn('id', []);

echo "4. Subjects Query Logic:\n";
echo "   - Has speciality relationships: " . ($hasSpecialityRelationships ? 'YES' : 'NO') . "\n";

if ($hasSpecialityRelationships) {
    echo "   - Team has speciality IDs: " . ($teamSpecialityIds->isNotEmpty() ? 'YES' : 'NO') . "\n";

    if ($teamSpecialityIds->isNotEmpty()) {
        echo "   - ✅ Query: Show subjects matching team specialities\n";
        $subjectsQuery->whereHas('specialities', function($q) use ($teamSpecialityIds) {
            $q->whereIn('specialities.id', $teamSpecialityIds);
        });
    } else {
        echo "   - ❌ Query: Show NO subjects (team has no specialities but relationships exist)\n";
        $subjectsQuery->whereRaw('1 = 0'); // This makes the query return no results
    }
} else {
    echo "   - ✅ Query: Show all validated subjects (no speciality relationships)\n";
}

$availableSubjects = $subjectsQuery->get();

echo "\n5. Final Results:\n";
echo "   - Available subjects count: " . $availableSubjects->count() . "\n";
echo "   - Total validated subjects: " . Subject::where('status', 'validated')->count() . "\n\n";

echo "=== Solution ===\n";
echo "If no subjects are available:\n";
echo "1. Either assign speciality_id to team members\n";
echo "2. OR populate subject_specialities table\n";
echo "3. OR modify controller logic to show all subjects when team has no specialities\n";