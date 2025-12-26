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
use App\Models\TeamSubjectPreference;

echo "=== Detailed Subject Availability Debug ===\n\n";

// Get team
$team = Team::first();
if (!$team) {
    echo "❌ No teams found\n";
    exit;
}

echo "1. Team: " . $team->name . " (ID: " . $team->id . ")\n\n";

// Check existing preferences
$currentPreferences = $team->subjectPreferences ?? collect();
$existingSubjectIds = $currentPreferences->pluck('subject_id')->toArray();

echo "2. Current Preferences:\n";
echo "   - Count: " . $currentPreferences->count() . "\n";
echo "   - Subject IDs: [" . implode(', ', $existingSubjectIds) . "]\n\n";

// Check total subjects
$totalSubjects = Subject::count();
$validatedSubjects = Subject::where('status', 'validated')->count();

echo "3. Subjects Overview:\n";
echo "   - Total subjects: $totalSubjects\n";
echo "   - Validated subjects: $validatedSubjects\n\n";

// Replicate exact controller logic
echo "4. Controller Logic Replication:\n";

// Step 1: Base query
$subjectsQuery = Subject::where('status', 'validated')
    ->whereNotIn('id', $existingSubjectIds);

echo "   Step 1 - Base query (validated, not in preferences):\n";
$step1Count = clone $subjectsQuery;
echo "   - Count after step 1: " . $step1Count->count() . "\n";

// Step 2: Check speciality relationships
$hasSpecialityRelationships = \DB::table('subject_specialities')->exists();
echo "   Step 2 - Speciality relationships check:\n";
echo "   - Has relationships: " . ($hasSpecialityRelationships ? 'YES' : 'NO') . "\n";

if ($hasSpecialityRelationships) {
    // Step 3: Get team speciality IDs
    $teamSpecialityIds = $team->members()
        ->with('user')
        ->get()
        ->pluck('user.speciality_id')
        ->filter()
        ->unique();

    echo "   Step 3 - Team speciality IDs:\n";
    echo "   - Team speciality IDs: [" . $teamSpecialityIds->implode(', ') . "]\n";
    echo "   - Count: " . $teamSpecialityIds->count() . "\n";
    echo "   - Is not empty: " . ($teamSpecialityIds->isNotEmpty() ? 'YES' : 'NO') . "\n";

    if ($teamSpecialityIds->isNotEmpty()) {
        echo "   Step 4 - Apply speciality filter:\n";
        $subjectsQuery->whereHas('specialities', function($q) use ($teamSpecialityIds) {
            $q->whereIn('specialities.id', $teamSpecialityIds);
        });
        echo "   - Applied whereHas filter for specialities\n";
    } else {
        echo "   Step 4 - No specialities, no subjects shown:\n";
        echo "   - ❌ Team has no specialities but relationships exist\n";
        echo "   - Result: Will show 0 subjects\n";
    }
} else {
    echo "   Step 3/4 - No speciality filtering:\n";
    echo "   - ✅ No speciality relationships exist, show all validated subjects\n";
}

$finalSubjects = $subjectsQuery->with('teacher')->get();

echo "\n5. Final Results:\n";
echo "   - Available subjects count: " . $finalSubjects->count() . "\n";

if ($finalSubjects->count() > 0) {
    echo "   - Subject names:\n";
    foreach ($finalSubjects as $subject) {
        echo "     * " . $subject->title . " (ID: " . $subject->id . ")\n";
    }
} else {
    echo "   - ❌ NO SUBJECTS AVAILABLE\n";
}

echo "\n=== Diagnosis ===\n";
if ($hasSpecialityRelationships) {
    echo "⚠️  ISSUE FOUND: subject_specialities table has data but team has no speciality assignments\n";
    echo "Solutions:\n";
    echo "1. Assign speciality_id to team members\n";
    echo "2. OR remove all records from subject_specialities table\n";
    echo "3. OR modify controller to show all subjects when team has no specialities\n";
} else {
    echo "✅ Logic should work correctly - all validated subjects should be shown\n";
    if ($finalSubjects->count() === 0) {
        echo "⚠️  But no subjects are available - check if subjects exist with status='validated'\n";
    }
}