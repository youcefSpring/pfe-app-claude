<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\DefenseController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ConflictController;
use App\Http\Controllers\Api\SubmissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - PFE Management System
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the PFE management system.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group with rate limiting and CORS.
|
*/

// =========================================================================
// PUBLIC API ENDPOINTS (No Authentication Required)
// =========================================================================

// Authentication endpoints
Route::prefix('auth')->name('api.auth.')->group(function () {
    Route::post('/login', [AuthController::class, 'apiLogin'])->name('login');

    // Password reset endpoints (if implementing)
    // Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    // Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0',
    ]);
})->name('api.health');

// =========================================================================
// AUTHENTICATED API ENDPOINTS
// =========================================================================

Route::middleware(['auth', 'throttle:api'])->group(function () {

    // =====================================================================
    // AUTHENTICATION & USER MANAGEMENT
    // =====================================================================

    Route::prefix('auth')->name('api.auth.')->group(function () {
        Route::post('/logout', [AuthController::class, 'apiLogout'])->name('logout');
        Route::get('/me', [AuthController::class, 'me'])->name('me');
        Route::put('/profile', [AuthController::class, 'updateProfile'])->name('update-profile');
        Route::put('/change-password', [AuthController::class, 'changePassword'])->name('change-password');

        // Notifications
        Route::get('/notifications', [AuthController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/{notification}/read', [AuthController::class, 'markNotificationRead'])->name('mark-notification-read');
        Route::post('/notifications/mark-all-read', [AuthController::class, 'markAllNotificationsRead'])->name('mark-all-notifications-read');
    });

    // =====================================================================
    // SUBJECT MANAGEMENT API
    // =====================================================================

    Route::apiResource('subjects', SubjectController::class);

    Route::prefix('subjects')->name('api.subjects.')->group(function () {
        // Subject workflow actions
        Route::post('/{subject}/submit', [SubjectController::class, 'submitForValidation'])->name('submit');
        Route::post('/{subject}/validate', [SubjectController::class, 'validate'])->name('validate');

        // Subject availability and filters
        Route::get('/available', [SubjectController::class, 'available'])->name('available');
        Route::get('/pending-validation', [SubjectController::class, 'pendingValidation'])->name('pending-validation');

        // Batch operations (department heads only)
        Route::post('/batch-validate', [SubjectController::class, 'batchValidate'])->name('batch-validate');
    });

    // =====================================================================
    // TEAM MANAGEMENT API
    // =====================================================================

    Route::apiResource('teams', TeamController::class);

    Route::prefix('teams')->name('api.teams.')->group(function () {
        // Team member management
        Route::post('/{team}/members', [TeamController::class, 'addMember'])->name('add-member');
        Route::delete('/{team}/members/{member}', [TeamController::class, 'removeMember'])->name('remove-member');

        // Subject selection
        Route::post('/{team}/select-subject', [TeamController::class, 'selectSubject'])->name('select-subject');

        // Team invitations
        Route::get('/invitations', [TeamController::class, 'invitations'])->name('invitations');
        Route::post('/{team}/accept-invitation', [TeamController::class, 'acceptInvitation'])->name('accept-invitation');
        Route::post('/{team}/decline-invitation', [TeamController::class, 'declineInvitation'])->name('decline-invitation');
    });

    // =====================================================================
    // PROJECT MANAGEMENT API
    // =====================================================================

    Route::apiResource('projects', ProjectController::class);

    Route::prefix('projects')->name('api.projects.')->group(function () {
        // Project workflow actions
        Route::post('/{project}/start', [ProjectController::class, 'start'])->name('start');
        Route::post('/{project}/submit', [ProjectController::class, 'submit'])->name('submit');
        Route::post('/{project}/complete', [ProjectController::class, 'complete'])->name('complete');

        // Project supervision
        Route::post('/{project}/assign-supervisor', [ProjectController::class, 'assignSupervisor'])->name('assign-supervisor');
        Route::get('/{project}/timeline', [ProjectController::class, 'timeline'])->name('timeline');
        Route::get('/{project}/progress', [ProjectController::class, 'progress'])->name('progress');

        // Supervisor actions
        Route::get('/supervised', [ProjectController::class, 'supervised'])->name('supervised');
        Route::post('/{project}/review', [ProjectController::class, 'review'])->name('review');
    });

    // =====================================================================
    // SUBMISSION MANAGEMENT API
    // =====================================================================

    Route::apiResource('submissions', SubmissionController::class);

    Route::prefix('submissions')->name('api.submissions.')->group(function () {
        // Submission workflow
        Route::post('/{submission}/approve', [SubmissionController::class, 'approve'])->name('approve');
        Route::post('/{submission}/reject', [SubmissionController::class, 'reject'])->name('reject');
        Route::post('/{submission}/grade', [SubmissionController::class, 'grade'])->name('grade');

        // File handling
        Route::get('/{submission}/download', [SubmissionController::class, 'download'])->name('download');
        Route::post('/{submission}/resubmit', [SubmissionController::class, 'resubmit'])->name('resubmit');
    });

    // =====================================================================
    // DEFENSE MANAGEMENT API
    // =====================================================================

    Route::apiResource('defenses', DefenseController::class);

    Route::prefix('defenses')->name('api.defenses.')->group(function () {
        // Defense scheduling
        Route::post('/schedule', [DefenseController::class, 'schedule'])->name('schedule');
        Route::post('/{defense}/reschedule', [DefenseController::class, 'reschedule'])->name('reschedule');
        Route::post('/{defense}/cancel', [DefenseController::class, 'cancel'])->name('cancel');

        // Defense workflow
        Route::post('/{defense}/start', [DefenseController::class, 'start'])->name('start');
        Route::post('/{defense}/complete', [DefenseController::class, 'complete'])->name('complete');
        Route::post('/{defense}/grade', [DefenseController::class, 'grade'])->name('grade');

        // Calendar and availability
        Route::get('/calendar', [DefenseController::class, 'calendar'])->name('calendar');
        Route::get('/availability', [DefenseController::class, 'checkAvailability'])->name('availability');

        // Reports
        Route::get('/{defense}/report', [DefenseController::class, 'generateReport'])->name('generate-report');
        Route::get('/{defense}/report/download', [DefenseController::class, 'downloadReport'])->name('download-report');
    });

    // =====================================================================
    // CONFLICT RESOLUTION API
    // =====================================================================

    Route::apiResource('conflicts', ConflictController::class)->only(['index', 'show']);

    Route::prefix('conflicts')->name('api.conflicts.')->group(function () {
        Route::post('/{conflict}/resolve', [ConflictController::class, 'resolve'])->name('resolve');
        Route::get('/{conflict}/preview-resolution', [ConflictController::class, 'previewResolution'])->name('preview-resolution');
        Route::get('/department/{department}', [ConflictController::class, 'byDepartment'])->name('by-department');
    });

    // =====================================================================
    // USER MANAGEMENT API (Admin only)
    // =====================================================================

    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);

        Route::prefix('users')->name('api.users.')->group(function () {
            // User management actions
            Route::post('/{user}/activate', [UserController::class, 'activate'])->name('activate');
            Route::post('/{user}/deactivate', [UserController::class, 'deactivate'])->name('deactivate');
            Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');

            // Bulk operations
            Route::post('/bulk-create', [UserController::class, 'bulkCreate'])->name('bulk-create');
            Route::post('/bulk-import', [UserController::class, 'bulkImport'])->name('bulk-import');

            // User statistics and reports
            Route::get('/statistics', [UserController::class, 'statistics'])->name('statistics');
            Route::get('/export', [UserController::class, 'export'])->name('export');
        });
    });

    // =====================================================================
    // REPORTING AND ANALYTICS API
    // =====================================================================

    Route::prefix('reports')->name('api.reports.')->group(function () {
        // General statistics (accessible to all authenticated users)
        Route::get('/dashboard-stats', [SubjectController::class, 'dashboardStats'])->name('dashboard-stats');

        // Role-specific reports
        Route::middleware('role:admin,department_head')->group(function () {
            Route::get('/subjects', [SubjectController::class, 'subjectReport'])->name('subjects');
            Route::get('/teams', [TeamController::class, 'teamReport'])->name('teams');
            Route::get('/projects', [ProjectController::class, 'projectReport'])->name('projects');
            Route::get('/defenses', [DefenseController::class, 'defenseReport'])->name('defenses');

            // Advanced analytics
            Route::get('/analytics/performance', [ProjectController::class, 'performanceAnalytics'])->name('performance-analytics');
            Route::get('/analytics/trends', [SubjectController::class, 'trendAnalytics'])->name('trend-analytics');
        });
    });

    // =====================================================================
    // EXTERNAL PROJECT API
    // =====================================================================

    Route::prefix('external-projects')->name('api.external-projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'externalProjects'])->name('index');
        Route::post('/', [ProjectController::class, 'submitExternalProject'])->name('submit');
        Route::get('/{externalProject}', [ProjectController::class, 'showExternalProject'])->name('show');
        Route::put('/{externalProject}', [ProjectController::class, 'updateExternalProject'])->name('update');

        // Approval workflow (department heads/admins only)
        Route::middleware('role:admin,department_head')->group(function () {
            Route::post('/{externalProject}/approve', [ProjectController::class, 'approveExternalProject'])->name('approve');
            Route::post('/{externalProject}/reject', [ProjectController::class, 'rejectExternalProject'])->name('reject');
        });
    });

    // =====================================================================
    // WORKFLOW AND STATUS API
    // =====================================================================

    Route::prefix('workflow')->name('api.workflow.')->group(function () {
        Route::get('/status', [AuthController::class, 'workflowStatus'])->name('status');
        Route::get('/next-actions', [AuthController::class, 'nextActions'])->name('next-actions');
        Route::get('/progress', [AuthController::class, 'progress'])->name('progress');
    });

    // =====================================================================
    // SYSTEM CONFIGURATION API (Admin only)
    // =====================================================================

    Route::prefix('system')->name('api.system.')->middleware('role:admin')->group(function () {
        Route::get('/settings', [UserController::class, 'getSettings'])->name('settings');
        Route::put('/settings', [UserController::class, 'updateSettings'])->name('update-settings');
        Route::get('/backup', [UserController::class, 'backup'])->name('backup');
        Route::get('/logs', [UserController::class, 'logs'])->name('logs');
    });
});

// =========================================================================
// RATE LIMITED PUBLIC ENDPOINTS
// =========================================================================

Route::middleware(['throttle:10,1'])->group(function () {
    // Public endpoints that need stricter rate limiting
    Route::get('/public/subjects/count', function () {
        return response()->json([
            'count' => \App\Models\Subject::where('status', 'validated')->count()
        ]);
    })->name('api.public.subjects.count');

    Route::get('/public/defenses/upcoming', function () {
        return response()->json([
            'count' => \App\Models\Defense::where('defense_date', '>', now())
                ->where('status', 'scheduled')
                ->count()
        ]);
    })->name('api.public.defenses.upcoming');
});