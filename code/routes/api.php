<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DefenseController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PFE API Routes
|--------------------------------------------------------------------------
|
| API routes for the PFE (Projet de Fin d'Études) platform.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group with Sanctum authentication.
|
*/

// =========================================================================
// PUBLIC AUTHENTICATION ROUTES
// =========================================================================

Route::prefix('v1/auth')->name('auth.')->group(function () {
    // Public authentication endpoints
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');

    // Protected authentication endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('me', [AuthController::class, 'me'])->name('me');
        Route::put('profile', [AuthController::class, 'updateProfile'])->name('profile');
        Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
    });
});

// =========================================================================
// PROTECTED API ROUTES (Authentication Required)
// =========================================================================

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

    // =====================================================================
    // SUBJECTS MANAGEMENT
    // =====================================================================

    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/', [SubjectController::class, 'index'])->name('index');
        Route::post('/', [SubjectController::class, 'store'])
            ->middleware('role:teacher')
            ->name('store');
        Route::get('{subject}', [SubjectController::class, 'show'])->name('show');
        Route::put('{subject}', [SubjectController::class, 'update'])->name('update');
        Route::delete('{subject}', [SubjectController::class, 'destroy'])->name('destroy');

        // Subject workflow actions
        Route::post('{subject}/submit', [SubjectController::class, 'submit'])->name('submit');
        Route::post('{subject}/validate', [SubjectController::class, 'validateSubject'])
            ->middleware('role:chef_master')
            ->name('validate');
        Route::post('{subject}/publish', [SubjectController::class, 'publish'])
            ->middleware('role:chef_master')
            ->name('publish');

        // Available subjects for teams
        Route::get('available/list', [SubjectController::class, 'available'])->name('available');
    });

    // =====================================================================
    // TEAMS MANAGEMENT
    // =====================================================================

    Route::prefix('teams')->name('teams.')->group(function () {
        Route::get('/', [TeamController::class, 'index'])->name('index');
        Route::post('/', [TeamController::class, 'store'])
            ->middleware('role:student')
            ->name('store');
        Route::get('{team}', [TeamController::class, 'show'])->name('show');
        Route::put('{team}', [TeamController::class, 'update'])->name('update');

        // Team members management
        Route::post('{team}/members', [TeamController::class, 'addMember'])->name('add-member');
        Route::delete('{team}/members/{user}', [TeamController::class, 'removeMember'])->name('remove-member');

        // Team preferences
        Route::post('{team}/preferences', [TeamController::class, 'setPreferences'])->name('preferences');
        Route::get('{team}/preferences', [TeamController::class, 'getPreferences'])->name('get-preferences');

        // Team validation
        Route::post('{team}/validate', [TeamController::class, 'validateTeam'])
            ->middleware('role:chef_master')
            ->name('validate');
    });

    // =====================================================================
    // PROJECTS MANAGEMENT
    // =====================================================================

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::post('/', [ProjectController::class, 'store'])
            ->middleware('role:chef_master')
            ->name('store');
        Route::get('{project}', [ProjectController::class, 'show'])->name('show');
        Route::put('{project}', [ProjectController::class, 'update'])->name('update');

        // Deliverables management
        Route::get('{project}/deliverables', [ProjectController::class, 'getDeliverables'])->name('deliverables');
        Route::post('{project}/deliverables', [ProjectController::class, 'uploadDeliverable'])->name('upload-deliverable');

        // External projects
        Route::post('{project}/external', [ProjectController::class, 'createExternalProject'])->name('external');

        // Project progress
        Route::get('{project}/progress', [ProjectController::class, 'getProgress'])->name('progress');
    });

    // Deliverables review (separate from projects for clarity)
    Route::prefix('deliverables')->name('deliverables.')->group(function () {
        Route::put('{deliverable}/review', [ProjectController::class, 'reviewDeliverable'])->name('review');
        Route::get('{deliverable}/download', [ProjectController::class, 'downloadDeliverable'])->name('download');
    });

    // =====================================================================
    // DEFENSE MANAGEMENT
    // =====================================================================

    Route::prefix('defenses')->name('defenses.')->group(function () {
        Route::get('/', [DefenseController::class, 'index'])->name('index');
        Route::post('/', [DefenseController::class, 'store'])
            ->middleware('role:admin_pfe')
            ->name('store');
        Route::get('{defense}', [DefenseController::class, 'show'])->name('show');
        Route::put('{defense}', [DefenseController::class, 'update'])->name('update');

        // Defense grading
        Route::post('{defense}/grades', [DefenseController::class, 'submitGrades'])->name('grades');

        // Defense PV generation
        Route::post('{defense}/pv', [DefenseController::class, 'generatePV'])
            ->middleware('role:admin_pfe')
            ->name('pv');

        // Auto-scheduling
        Route::post('auto-schedule', [DefenseController::class, 'autoSchedule'])
            ->middleware('role:admin_pfe')
            ->name('auto-schedule');

        // Available slots
        Route::get('available-slots', [DefenseController::class, 'getAvailableSlots'])->name('available-slots');
    });

    // =====================================================================
    // ADMINISTRATION
    // =====================================================================

    Route::prefix('admin')->middleware('role:admin_pfe|chef_master')->name('admin.')->group(function () {
        // User management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'getUsers'])->name('index');
            Route::post('/', [AdminController::class, 'createUser'])->name('store');
            Route::get('{user}', [AdminController::class, 'getUser'])->name('show');
            Route::put('{user}', [AdminController::class, 'updateUser'])->name('update');
            Route::delete('{user}', [AdminController::class, 'deleteUser'])->name('destroy');
            Route::put('{user}/roles', [AdminController::class, 'updateUserRoles'])->name('roles');
            Route::put('{user}/status', [AdminController::class, 'updateUserStatus'])->name('status');
        });

        // Rooms management
        Route::prefix('rooms')->name('rooms.')->group(function () {
            Route::get('/', [AdminController::class, 'getRooms'])->name('index');
            Route::post('/', [AdminController::class, 'createRoom'])->name('store');
            Route::get('{room}', [AdminController::class, 'getRoom'])->name('show');
            Route::put('{room}', [AdminController::class, 'updateRoom'])->name('update');
            Route::delete('{room}', [AdminController::class, 'deleteRoom'])->name('destroy');
        });

        // System statistics
        Route::get('stats', [AdminController::class, 'getStats'])->name('stats');
        Route::get('dashboard', [AdminController::class, 'getDashboardData'])->name('dashboard');

        // Conflict resolution
        Route::prefix('conflicts')->name('conflicts.')->group(function () {
            Route::get('/', [AdminController::class, 'getConflicts'])->name('index');
            Route::post('resolve', [AdminController::class, 'resolveConflict'])
                ->middleware('role:chef_master')
                ->name('resolve');
        });

        // Project assignments
        Route::post('assign-projects', [AdminController::class, 'assignProjects'])
            ->middleware('role:chef_master')
            ->name('assign-projects');
    });

    // =====================================================================
    // NOTIFICATIONS
    // =====================================================================

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('unread', [NotificationController::class, 'getUnread'])->name('unread');
        Route::put('{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('{notification}', [NotificationController::class, 'destroy'])->name('destroy');

        // Notification preferences
        Route::get('preferences', [NotificationController::class, 'getPreferences'])->name('preferences');
        Route::put('preferences', [NotificationController::class, 'updatePreferences'])->name('update-preferences');
    });

    // =====================================================================
    // FILE MANAGEMENT
    // =====================================================================

    Route::prefix('files')->name('files.')->group(function () {
        Route::post('upload', [FileController::class, 'upload'])->name('upload');
        Route::get('{path}', [FileController::class, 'download'])
            ->where('path', '.*')
            ->name('download');
        Route::delete('{path}', [FileController::class, 'delete'])
            ->where('path', '.*')
            ->name('delete');

        // File metadata
        Route::get('{path}/info', [FileController::class, 'getFileInfo'])
            ->where('path', '.*')
            ->name('info');
    });

    // =====================================================================
    // SEARCH & FILTERS
    // =====================================================================

    Route::prefix('search')->name('search.')->group(function () {
        Route::get('/', [SearchController::class, 'search'])->name('global');
        Route::get('quick', [SearchController::class, 'quick'])->name('quick');
        Route::post('advanced', [SearchController::class, 'advanced'])->name('advanced');
    });

    // =====================================================================
    // ANALYTICS & REPORTS
    // =====================================================================

    Route::prefix('reports')->middleware('role:admin_pfe|chef_master|teacher')->name('reports.')->group(function () {
        // Defense reports
        Route::get('defense-schedule', [ReportController::class, 'defenseSchedule'])->name('defense-schedule');
        Route::get('defense-statistics', [ReportController::class, 'defenseStatistics'])->name('defense-statistics');

        // Project reports
        Route::get('project-progress', [ReportController::class, 'projectProgress'])->name('project-progress');
        Route::get('project-assignments', [ReportController::class, 'projectAssignments'])->name('project-assignments');

        // Team reports
        Route::get('team-performance', [ReportController::class, 'teamPerformance'])->name('team-performance');
        Route::get('team-statistics', [ReportController::class, 'teamStatistics'])->name('team-statistics');

        // Subject reports
        Route::get('subject-analysis', [ReportController::class, 'subjectAnalysis'])->name('subject-analysis');
        Route::get('subject-demand', [ReportController::class, 'subjectDemand'])->name('subject-demand');

        // System reports
        Route::get('system-usage', [ReportController::class, 'systemUsage'])->name('system-usage');
        Route::get('user-activity', [ReportController::class, 'userActivity'])->name('user-activity');

        // Export functionality
        Route::get('{report}/export', [ReportController::class, 'export'])
            ->where('report', '[a-zA-Z0-9\-_]+')
            ->name('export');
    });

    // =====================================================================
    // DASHBOARD DATA
    // =====================================================================

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('stats', [ReportController::class, 'getDashboardStats'])->name('stats');
        Route::get('recent-activity', [ReportController::class, 'getRecentActivity'])->name('activity');
        Route::get('notifications-summary', [NotificationController::class, 'getSummary'])->name('notifications');
        Route::get('calendar-events', [DefenseController::class, 'getCalendarEvents'])->name('calendar');
    });
});

// =========================================================================
// RATE LIMITED ROUTES
// =========================================================================

Route::middleware(['throttle:60,1'])->group(function () {
    // Rate limited endpoints (60 requests per minute)
    Route::prefix('v1')->group(function () {
        Route::post('auth/login', [AuthController::class, 'login']);
        Route::post('auth/register', [AuthController::class, 'register']);
    });
});

Route::middleware(['throttle:30,1'])->group(function () {
    // More strictly rate limited endpoints (30 requests per minute)
    Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
        Route::post('files/upload', [FileController::class, 'upload']);
        Route::post('reports/*/export', [ReportController::class, 'export']);
    });
});

// =========================================================================
// HEALTH CHECK & STATUS
// =========================================================================

Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '1.0.0')
    ]);
})->name('health');