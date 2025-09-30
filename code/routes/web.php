<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\SubjectController;
use App\Http\Controllers\Web\TeamController;
use App\Http\Controllers\Web\ProjectController;
use App\Http\Controllers\Web\DefenseController;
use App\Http\Controllers\Web\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - PFE Management System
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the PFE (Final Year Project)
| management system. These routes are loaded by the RouteServiceProvider
| and all of them will be assigned to the "web" middleware group.
|
*/

// =========================================================================
// PUBLIC ROUTES (No Authentication Required)
// =========================================================================

// Landing page - redirect to login
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// =========================================================================
// AUTHENTICATED WEB ROUTES
// =========================================================================

Route::middleware(['auth'])->group(function () {

    // =====================================================================
    // DASHBOARD ROUTES
    // =====================================================================

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Role-specific dashboards
    Route::get('/dashboard/student', [DashboardController::class, 'student'])
        ->name('dashboard.student')
        ->middleware('role:student');

    Route::get('/dashboard/teacher', [DashboardController::class, 'teacher'])
        ->name('dashboard.teacher')
        ->middleware('role:teacher');

    Route::get('/dashboard/department-head', [DashboardController::class, 'departmentHead'])
        ->name('dashboard.department-head')
        ->middleware('role:department_head');

    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->name('dashboard.admin')
        ->middleware('role:admin');

    // =====================================================================
    // SUBJECT MANAGEMENT ROUTES
    // =====================================================================

    Route::prefix('subjects')->name('subjects.')->group(function () {
        // Static routes first
        Route::get('/', [SubjectController::class, 'index'])->name('index');
        Route::get('/available/{grade?}', [SubjectController::class, 'available'])->name('available');

        // Teachers can create and manage their subjects
        Route::middleware('role:teacher')->group(function () {
            Route::get('/create', [SubjectController::class, 'create'])->name('create');
            Route::post('/', [SubjectController::class, 'store'])->name('store');
        });

        // Dynamic route for viewing specific subjects (must be after static routes)
        Route::get('/{subject}', [SubjectController::class, 'show'])->name('show');

        // Teacher routes that require subject parameter
        Route::middleware('role:teacher')->group(function () {
            Route::get('/{subject}/edit', [SubjectController::class, 'edit'])->name('edit');
            Route::put('/{subject}', [SubjectController::class, 'update'])->name('update');
            Route::delete('/{subject}', [SubjectController::class, 'destroy'])->name('destroy');
            Route::post('/{subject}/submit-validation', [SubjectController::class, 'submitForValidation'])->name('submit-validation');
        });

        // Department heads can validate subjects
        Route::middleware('role:department_head')->group(function () {
            Route::get('/validation/pending', [SubjectController::class, 'pendingValidation'])->name('pending-validation');
            Route::post('/{subject}/validate', [SubjectController::class, 'validate'])->name('validate');
            Route::post('/batch-validate', [SubjectController::class, 'batchValidate'])->name('batch-validate');
        });
    });

    // =====================================================================
    // TEAM MANAGEMENT ROUTES
    // =====================================================================

    Route::prefix('teams')->name('teams.')->group(function () {
        // All users can view teams
        Route::get('/', [TeamController::class, 'index'])->name('index');

        // Students can create and manage teams (static routes before dynamic ones)
        Route::middleware('role:student')->group(function () {
            Route::get('/create', [TeamController::class, 'create'])->name('create');
            Route::post('/', [TeamController::class, 'store'])->name('store');
            Route::get('/{team}/edit', [TeamController::class, 'edit'])->name('edit');
            Route::put('/{team}', [TeamController::class, 'update'])->name('update');
            Route::delete('/{team}', [TeamController::class, 'destroy'])->name('destroy');

            // Team member management
            Route::post('/{team}/members', [TeamController::class, 'addMember'])->name('add-member');
            Route::delete('/{team}/members/{member}', [TeamController::class, 'removeMember'])->name('remove-member');
            Route::post('/{team}/invite', [TeamController::class, 'sendInvitation'])->name('send-invitation');

            // Subject selection
            Route::get('/{team}/select-subject', [TeamController::class, 'selectSubjectForm'])->name('select-subject-form');
            Route::post('/{team}/select-subject', [TeamController::class, 'selectSubject'])->name('select-subject');

            // External project submission
            Route::get('/{team}/external-project', [TeamController::class, 'externalProjectForm'])->name('external-project-form');
            Route::post('/{team}/external-project', [TeamController::class, 'submitExternalProject'])->name('external-project');

            // Team actions
            Route::post('/{team}/join', [TeamController::class, 'join'])->name('join');
            Route::post('/{team}/leave', [TeamController::class, 'leave'])->name('leave');
            Route::post('/{team}/transfer-leadership', [TeamController::class, 'transferLeadership'])->name('transfer-leadership');

            // Team invitations
            Route::get('/invitations', [TeamController::class, 'invitations'])->name('invitations');
            Route::post('/{team}/accept-invitation', [TeamController::class, 'acceptInvitation'])->name('accept-invitation');
            Route::post('/{team}/decline-invitation', [TeamController::class, 'declineInvitation'])->name('decline-invitation');
        });

        // Dynamic route for viewing teams (must be after static routes)
        Route::get('/{team}', [TeamController::class, 'show'])->name('show');
    });

    // =====================================================================
    // PROJECT MANAGEMENT ROUTES
    // =====================================================================

    Route::prefix('projects')->name('projects.')->group(function () {
        // Static routes first
        Route::get('/', [ProjectController::class, 'index'])->name('index');

        // Teachers can supervise and review projects (static routes)
        Route::middleware('role:teacher')->group(function () {
            Route::get('/supervised', [ProjectController::class, 'supervised'])->name('supervised');
        });

        // Dynamic route for viewing specific projects (must be after static routes)
        Route::get('/{project}', [ProjectController::class, 'show'])->name('show');

        // Students can manage their project submissions
        Route::middleware('role:student')->group(function () {
            Route::get('/{project}/submissions', [ProjectController::class, 'submissions'])->name('submissions');
            Route::get('/{project}/submit', [ProjectController::class, 'submitForm'])->name('submit-form');
            Route::post('/{project}/submit', [ProjectController::class, 'submit'])->name('submit');
            Route::get('/{project}/timeline', [ProjectController::class, 'timeline'])->name('timeline');
        });

        // Teacher routes that require project parameter
        Route::middleware('role:teacher')->group(function () {
            Route::get('/{project}/review', [ProjectController::class, 'reviewForm'])->name('review-form');
            Route::post('/{project}/review', [ProjectController::class, 'submitReview'])->name('submit-review');
            Route::post('/{project}/submissions/{submission}/grade', [ProjectController::class, 'gradeSubmission'])->name('grade-submission');
        });

        // Download routes (accessible by authorized users)
        Route::get('/{project}/submissions/{submission}/download/{filename}', [ProjectController::class, 'downloadSubmission'])
            ->name('download-submission');

        // Admins and department heads can create projects
        Route::middleware('role:admin,department_head')->group(function () {
            Route::get('/create', [ProjectController::class, 'create'])->name('create');
            Route::post('/', [ProjectController::class, 'store'])->name('store');
            Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
            Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
            Route::post('/{project}/assign-supervisor', [ProjectController::class, 'assignSupervisor'])->name('assign-supervisor');
        });
    });

    // =====================================================================
    // SUBMISSION MANAGEMENT ROUTES
    // =====================================================================

    Route::prefix('submissions')->name('submissions.')->group(function () {
        // Teachers can view and grade submissions
        Route::middleware('role:teacher')->group(function () {
            Route::get('/', [ProjectController::class, 'allSubmissions'])->name('index');
            Route::get('/{submission}', [ProjectController::class, 'showSubmission'])->name('show');
        });
    });

    // =====================================================================
    // DEFENSE MANAGEMENT ROUTES
    // =====================================================================

    Route::prefix('defenses')->name('defenses.')->group(function () {
        // All users can view defenses
        Route::get('/', [DefenseController::class, 'index'])->name('index');
        Route::get('/calendar', [DefenseController::class, 'calendar'])->name('calendar');
        Route::get('/{defense}', [DefenseController::class, 'show'])->name('show');

        // Students can view their defense details
        Route::middleware('role:student')->group(function () {
            Route::get('/my-defense', [DefenseController::class, 'myDefense'])->name('my-defense');
        });

        // Teachers can view jury assignments
        Route::middleware('role:teacher')->group(function () {
            Route::get('/jury-assignments', [DefenseController::class, 'juryAssignments'])->name('jury-assignments');
        });

        // Admins can schedule and manage defenses
        Route::middleware('role:admin,department_head')->group(function () {
            Route::get('/schedule', [DefenseController::class, 'scheduleForm'])->name('schedule-form');
            Route::post('/schedule', [DefenseController::class, 'schedule'])->name('schedule');
            Route::post('/auto-schedule', [DefenseController::class, 'autoSchedule'])->name('auto-schedule');
            Route::post('/{defense}/grade', [DefenseController::class, 'submitGrade'])->name('submit-grade');
            Route::get('/{defense}/edit', [DefenseController::class, 'edit'])->name('edit');
            Route::put('/{defense}', [DefenseController::class, 'update'])->name('update');
            Route::delete('/{defense}', [DefenseController::class, 'cancel'])->name('cancel');
            Route::post('/{defense}/complete', [DefenseController::class, 'complete'])->name('complete');
            Route::get('/{defense}/report', [DefenseController::class, 'generateReport'])->name('generate-report');
        });
    });

    // =====================================================================
    // CONFLICT RESOLUTION ROUTES
    // =====================================================================

    Route::prefix('conflicts')->name('conflicts.')->middleware('role:department_head')->group(function () {
        Route::get('/', [SubjectController::class, 'conflicts'])->name('index');
        Route::get('/{conflict}', [SubjectController::class, 'showConflict'])->name('show');
        Route::post('/{conflict}/resolve', [SubjectController::class, 'resolveConflict'])->name('resolve');
    });

    // =====================================================================
    // ADMINISTRATION ROUTES
    // =====================================================================

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        // User Management
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
        Route::get('/users/bulk-import', [AdminController::class, 'bulkImport'])->name('users.bulk-import');
        Route::post('/users/bulk-import', [AdminController::class, 'processBulkImport'])->name('users.bulk-import.process');

        // Student Management
        Route::get('/students/upload', [AdminController::class, 'studentsUpload'])->name('students.upload');
        Route::post('/students/upload', [AdminController::class, 'studentsUploadProcess'])->name('students.upload.process');

        // Speciality Management
        Route::get('/specialities', [AdminController::class, 'specialities'])->name('specialities');
        Route::get('/specialities/create', [AdminController::class, 'createSpeciality'])->name('specialities.create');
        Route::post('/specialities', [AdminController::class, 'storeSpeciality'])->name('specialities.store');
        Route::get('/specialities/{speciality}/edit', [AdminController::class, 'editSpeciality'])->name('specialities.edit');
        Route::put('/specialities/{speciality}', [AdminController::class, 'updateSpeciality'])->name('specialities.update');
        Route::delete('/specialities/{speciality}', [AdminController::class, 'destroySpeciality'])->name('specialities.destroy');

        // System Configuration
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');

        // Reports and Analytics
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/reports/generate', [AdminController::class, 'generateReport'])->name('reports.generate');
        Route::get('/reports/subjects', [AdminController::class, 'subjectsReport'])->name('reports.subjects');
        Route::get('/reports/teams', [AdminController::class, 'teamsReport'])->name('reports.teams');
        Route::get('/reports/projects', [AdminController::class, 'projectsReport'])->name('reports.projects');
        Route::get('/reports/defenses', [AdminController::class, 'defensesReport'])->name('reports.defenses');
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
        Route::get('/logs', [AdminController::class, 'logs'])->name('logs');

        // Room Management
        Route::get('/rooms', [AdminController::class, 'rooms'])->name('rooms');
        Route::get('/rooms/create', [AdminController::class, 'createRoom'])->name('rooms.create');
        Route::post('/rooms', [AdminController::class, 'storeRoom'])->name('rooms.store');
        Route::get('/rooms/{room}/edit', [AdminController::class, 'editRoom'])->name('rooms.edit');
        Route::put('/rooms/{room}', [AdminController::class, 'updateRoom'])->name('rooms.update');
        Route::delete('/rooms/{room}', [AdminController::class, 'destroyRoom'])->name('rooms.destroy');

        // Backup and Maintenance
        Route::get('/maintenance', [AdminController::class, 'maintenance'])->name('maintenance');
        Route::post('/backup', [AdminController::class, 'backup'])->name('backup');
    });

    // =====================================================================
    // PROFILE AND NOTIFICATION ROUTES
    // =====================================================================

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [AuthController::class, 'profile'])->name('show');
        Route::put('/', [AuthController::class, 'updateProfile'])->name('update');
        Route::put('/password', [AuthController::class, 'changePassword'])->name('change-password');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [AuthController::class, 'notifications'])->name('index');
        Route::post('/{notification}/read', [AuthController::class, 'markNotificationRead'])->name('mark-read');
        Route::post('/mark-all-read', [AuthController::class, 'markAllNotificationsRead'])->name('mark-all-read');
    });
});

// =========================================================================
// FALLBACK ROUTES
// =========================================================================

// Redirect any undefined routes to dashboard for authenticated users
Route::fallback(function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});