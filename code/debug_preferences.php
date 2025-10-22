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
use App\Models\AllocationDeadline;
use Carbon\Carbon;

echo "=== Subject Preferences Debug Script ===\n\n";

// Get all teams
$teams = Team::with(['members.user', 'subjectPreferences'])->get();

echo "Total teams found: " . $teams->count() . "\n\n";

if ($teams->isEmpty()) {
    echo "No teams found. Please create a team first.\n";
    exit;
}

// Check allocation deadline
$deadline = AllocationDeadline::active()->first();

echo "=== Allocation Deadline Check ===\n";
if (!$deadline) {
    echo "❌ No active allocation deadline found\n";
    echo "Solution: Create an active allocation deadline in admin panel\n\n";
} else {
    echo "✅ Active deadline found: " . $deadline->title . "\n";
    echo "Status: " . $deadline->status . "\n";
    echo "Preferences Start: " . $deadline->preferences_start . "\n";
    echo "Preferences Deadline: " . $deadline->preferences_deadline . "\n";
    echo "Current Time: " . now() . "\n";

    $canStudentsChoose = $deadline->canStudentsChoose();
    echo "Can Students Choose: " . ($canStudentsChoose ? '✅ YES' : '❌ NO') . "\n";

    if (!$canStudentsChoose) {
        $now = now();
        if ($now->lt($deadline->preferences_start)) {
            echo "❌ Current time is before preferences start date\n";
        } elseif ($now->gt($deadline->preferences_deadline)) {
            echo "❌ Current time is after preferences deadline\n";
        }
    }
    echo "\n";
}

// Check each team
foreach ($teams as $team) {
    echo "=== Team: " . $team->name . " ===\n";

    // Check 1: Team completion
    echo "1. Team Completion Check:\n";
    $isComplete = $team->isComplete();
    echo "   Is Complete: " . ($isComplete ? '✅ YES' : '❌ NO') . "\n";
    echo "   Member Count: " . $team->members->count() . "\n";

    if (!$isComplete) {
        echo "   ❌ Team needs more members to be complete\n";
    }

    // Check 2: Deadline check (already done above, but show result)
    echo "2. Deadline Check:\n";
    echo "   Can Choose: " . (($deadline && $deadline->canStudentsChoose()) ? '✅ YES' : '❌ NO') . "\n";

    // Check 3: Subject allocation check
    echo "3. Subject Allocation Check:\n";
    $hasAllocatedSubject = $team->subjectPreferences()->whereNotNull('allocated_at')->exists();
    echo "   Has Allocated Subject: " . ($hasAllocatedSubject ? '❌ YES (blocking)' : '✅ NO (good)') . "\n";

    if ($hasAllocatedSubject) {
        $allocatedPref = $team->subjectPreferences()->whereNotNull('allocated_at')->first();
        echo "   Allocated Subject: " . $allocatedPref->subject->title . "\n";
        echo "   Allocated At: " . $allocatedPref->allocated_at . "\n";
    }

    // Final result
    echo "4. Final Result:\n";
    $canManage = $team->canManagePreferences();
    echo "   Can Manage Preferences: " . ($canManage ? '✅ YES' : '❌ NO') . "\n";

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "=== Solutions ===\n";
echo "If preferences are blocked, check:\n";
echo "1. Ensure team has the required number of members\n";
echo "2. Create/activate an allocation deadline with valid date range\n";
echo "3. Make sure no subject has been allocated to the team yet\n";
echo "4. Check server timezone settings vs database timezone\n";