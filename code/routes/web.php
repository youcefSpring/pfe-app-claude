<?php

use App\Http\Controllers\Admin\BlogPostController as AdminBlogPostController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Public\BlogController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\CourseController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProjectController;
use App\Http\Controllers\Public\PublicationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// =========================================================================
// PUBLIC ROUTES (No Authentication Required)
// =========================================================================

// Homepage and About
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/download-cv', [HomeController::class, 'downloadCV'])->name('download-cv');

// Courses
Route::name('courses.')->group(function () {
    Route::get('/courses', [CourseController::class, 'index'])->name('index');
    Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('show');
    Route::get('/courses/{course:slug}/syllabus', [CourseController::class, 'downloadSyllabus'])->name('syllabus');
});

// Projects Portfolio
Route::name('projects.')->group(function () {
    Route::get('/projects', [ProjectController::class, 'index'])->name('index');
    Route::get('/projects/{project:slug}', [ProjectController::class, 'show'])->name('show');
});

// Publications
Route::name('publications.')->group(function () {
    Route::get('/publications', [PublicationController::class, 'index'])->name('index');
    Route::get('/publications/{publication}', [PublicationController::class, 'show'])->name('show');
    Route::get('/publications/{publication}/download', [PublicationController::class, 'download'])->name('download');
});

// Blog
Route::name('blog.')->group(function () {
    Route::get('/blog', [BlogController::class, 'index'])->name('index');
    Route::get('/blog/{blogPost:slug}', [BlogController::class, 'show'])->name('show');
});

// Contact
Route::name('contact.')->group(function () {
    Route::get('/contact', [ContactController::class, 'show'])->name('show');
    Route::post('/contact', [ContactController::class, 'store'])->name('store');
});

// =========================================================================
// AUTHENTICATION ROUTES
// =========================================================================

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// =========================================================================
// ADMIN ROUTES (Authentication Required)
// =========================================================================

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Courses Management
    Route::resource('courses', AdminCourseController::class)->except(['show']);

    // Projects Management
    Route::resource('projects', AdminProjectController::class);

    // Blog Posts Management
    Route::resource('blog', AdminBlogPostController::class);

    // Contact Messages Management
    Route::name('contact.')->group(function () {
        Route::get('/contact', [ContactMessageController::class, 'index'])->name('index');
        Route::get('/contact/{contactMessage}', [ContactMessageController::class, 'show'])->name('show');
        Route::put('/contact/{contactMessage}/status', [ContactMessageController::class, 'updateStatus'])->name('update-status');
        Route::delete('/contact/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('destroy');

        // Bulk Operations
        Route::post('/contact/bulk-status', [ContactMessageController::class, 'bulkUpdateStatus'])->name('bulk-status');
        Route::delete('/contact/bulk-delete', [ContactMessageController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Publications Management
    Route::resource('publications', \App\Http\Controllers\Admin\PublicationController::class);

    // Tags Management
    Route::resource('tags', \App\Http\Controllers\Admin\TagController::class)->except(['show']);

    // Profile Management
    Route::name('profile.')->group(function () {
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('edit');
        Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('update');
        Route::post('/profile/cv', [\App\Http\Controllers\Admin\ProfileController::class, 'uploadCV'])->name('upload-cv');
        Route::delete('/profile/cv', [\App\Http\Controllers\Admin\ProfileController::class, 'deleteCV'])->name('delete-cv');
    });

    // Media Management
    Route::name('media.')->group(function () {
        Route::post('/media/upload', [\App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('upload');
        Route::delete('/media/{file}', [\App\Http\Controllers\Admin\MediaController::class, 'delete'])->name('delete');
    });
});

// =========================================================================
// PFE PLATFORM ROUTES (Authentication Required)
// =========================================================================

Route::middleware(['auth'])->prefix('pfe')->name('pfe.')->group(function () {

    // PFE Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Web\DashboardController::class, 'index'])->name('dashboard');

    // Role-specific dashboards
    Route::get('/dashboard/student', [\App\Http\Controllers\Web\DashboardController::class, 'student'])
        ->middleware('role:student')
        ->name('dashboard.student');
    Route::get('/dashboard/teacher', [\App\Http\Controllers\Web\DashboardController::class, 'teacher'])
        ->middleware('role:teacher')
        ->name('dashboard.teacher');
    Route::get('/dashboard/admin', [\App\Http\Controllers\Web\DashboardController::class, 'admin'])
        ->middleware('role:admin_pfe|chef_master')
        ->name('dashboard.admin');

    // =====================================================================
    // SUBJECTS MANAGEMENT
    // =====================================================================

    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\SubjectController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Web\SubjectController::class, 'create'])
            ->middleware('role:teacher')
            ->name('create');
        Route::post('/', [\App\Http\Controllers\Web\SubjectController::class, 'store'])
            ->middleware('role:teacher')
            ->name('store');
        Route::get('/{subject}', [\App\Http\Controllers\Web\SubjectController::class, 'show'])->name('show');
        Route::get('/{subject}/edit', [\App\Http\Controllers\Web\SubjectController::class, 'edit'])->name('edit');
        Route::put('/{subject}', [\App\Http\Controllers\Web\SubjectController::class, 'update'])->name('update');
        Route::delete('/{subject}', [\App\Http\Controllers\Web\SubjectController::class, 'destroy'])->name('destroy');

        // Subject workflow actions
        Route::post('/{subject}/submit', [\App\Http\Controllers\Web\SubjectController::class, 'submit'])->name('submit');
        Route::get('/{subject}/validate', [\App\Http\Controllers\Web\SubjectController::class, 'showValidation'])
            ->middleware('role:chef_master')
            ->name('validation');
        Route::post('/{subject}/validate', [\App\Http\Controllers\Web\SubjectController::class, 'validate'])
            ->middleware('role:chef_master')
            ->name('validate');
        Route::post('/{subject}/publish', [\App\Http\Controllers\Web\SubjectController::class, 'publish'])
            ->middleware('role:chef_master')
            ->name('publish');

        // Available subjects for team selection
        Route::get('/available/list', [\App\Http\Controllers\Web\SubjectController::class, 'available'])
            ->middleware('role:student')
            ->name('available');
    });

    // =====================================================================
    // TEAMS MANAGEMENT
    // =====================================================================

    Route::prefix('teams')->name('teams.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\TeamController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Web\TeamController::class, 'create'])
            ->middleware('role:student')
            ->name('create');
        Route::post('/', [\App\Http\Controllers\Web\TeamController::class, 'store'])
            ->middleware('role:student')
            ->name('store');
        Route::get('/{team}', [\App\Http\Controllers\Web\TeamController::class, 'show'])->name('show');
        Route::get('/{team}/edit', [\App\Http\Controllers\Web\TeamController::class, 'edit'])->name('edit');
        Route::put('/{team}', [\App\Http\Controllers\Web\TeamController::class, 'update'])->name('update');

        // Team members management
        Route::post('/{team}/members', [\App\Http\Controllers\Web\TeamController::class, 'addMember'])->name('add-member');
        Route::delete('/{team}/members/{user}', [\App\Http\Controllers\Web\TeamController::class, 'removeMember'])->name('remove-member');

        // Team preferences
        Route::get('/{team}/preferences', [\App\Http\Controllers\Web\TeamController::class, 'showPreferences'])->name('preferences');
        Route::post('/{team}/preferences', [\App\Http\Controllers\Web\TeamController::class, 'setPreferences'])->name('set-preferences');

        // Team validation
        Route::post('/{team}/validate', [\App\Http\Controllers\Web\TeamController::class, 'validate'])
            ->middleware('role:chef_master')
            ->name('validate');

        // User's team
        Route::get('/my/team', [\App\Http\Controllers\Web\TeamController::class, 'myTeam'])
            ->middleware('role:student')
            ->name('my-team');
        Route::post('/my/leave', [\App\Http\Controllers\Web\TeamController::class, 'leave'])
            ->middleware('role:student')
            ->name('leave');
    });

    // =====================================================================
    // PROJECTS MANAGEMENT
    // =====================================================================

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\ProjectController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Web\ProjectController::class, 'create'])
            ->middleware('role:chef_master')
            ->name('create');
        Route::post('/', [\App\Http\Controllers\Web\ProjectController::class, 'store'])
            ->middleware('role:chef_master')
            ->name('store');
        Route::get('/{project}', [\App\Http\Controllers\Web\ProjectController::class, 'show'])->name('show');
        Route::get('/{project}/edit', [\App\Http\Controllers\Web\ProjectController::class, 'edit'])->name('edit');
        Route::put('/{project}', [\App\Http\Controllers\Web\ProjectController::class, 'update'])->name('update');

        // Deliverables management
        Route::get('/{project}/upload', [\App\Http\Controllers\Web\ProjectController::class, 'showUpload'])->name('upload');
        Route::post('/{project}/deliverables', [\App\Http\Controllers\Web\ProjectController::class, 'uploadDeliverable'])->name('upload-deliverable');

        // Deliverable review
        Route::get('/deliverables/{deliverable}/review', [\App\Http\Controllers\Web\ProjectController::class, 'showReview'])->name('review-deliverable');
        Route::post('/deliverables/{deliverable}/review', [\App\Http\Controllers\Web\ProjectController::class, 'reviewDeliverable'])->name('submit-review');
        Route::get('/deliverables/{deliverable}/download', [\App\Http\Controllers\Web\ProjectController::class, 'downloadDeliverable'])->name('download-deliverable');

        // User's project
        Route::get('/my/project', [\App\Http\Controllers\Web\ProjectController::class, 'myProject'])->name('my-project');
        Route::get('/{project}/progress', [\App\Http\Controllers\Web\ProjectController::class, 'progress'])->name('progress');
    });

    // =====================================================================
    // DEFENSE MANAGEMENT
    // =====================================================================

    Route::prefix('defenses')->middleware('role:admin_pfe|chef_master|teacher')->name('defenses.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\DefenseController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Web\DefenseController::class, 'create'])
            ->middleware('role:admin_pfe')
            ->name('create');
        Route::post('/', [\App\Http\Controllers\Web\DefenseController::class, 'store'])
            ->middleware('role:admin_pfe')
            ->name('store');
        Route::get('/{defense}', [\App\Http\Controllers\Web\DefenseController::class, 'show'])->name('show');
        Route::get('/{defense}/edit', [\App\Http\Controllers\Web\DefenseController::class, 'edit'])->name('edit');
        Route::put('/{defense}', [\App\Http\Controllers\Web\DefenseController::class, 'update'])->name('update');

        // Defense grading
        Route::get('/{defense}/grades', [\App\Http\Controllers\Web\DefenseController::class, 'showGrades'])->name('grades');
        Route::post('/{defense}/grades', [\App\Http\Controllers\Web\DefenseController::class, 'submitGrades'])->name('submit-grades');

        // Enhanced Defense scheduling
        Route::prefix('scheduling')->middleware('role:admin_pfe|chef_master')->name('scheduling.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\DefenseSchedulingController::class, 'index'])->name('index');
            Route::get('/auto', [\App\Http\Controllers\Web\DefenseSchedulingController::class, 'showAutoScheduling'])->name('auto');
            Route::post('/auto/execute', [\App\Http\Controllers\Web\DefenseSchedulingController::class, 'executeAutoScheduling'])->name('auto.execute');
            Route::get('/results', [\App\Http\Controllers\Web\DefenseSchedulingController::class, 'showResults'])->name('results');
            Route::get('/manual', [\App\Http\Controllers\Web\DefenseSchedulingController::class, 'showManualScheduling'])->name('manual');
            Route::get('/calendar', [\App\Http\Controllers\Web\DefenseSchedulingController::class, 'showCalendar'])->name('calendar');
            Route::post('/check-availability', [\App\Http\Controllers\Web\DefenseSchedulingController::class, 'checkAvailability'])->name('check-availability');
            Route::post('/bulk-operations', [\App\Http\Controllers\Web\DefenseSchedulingController::class, 'bulkOperations'])->name('bulk-operations');
            Route::get('/export', [\App\Http\Controllers\Web\DefenseSchedulingController::class, 'exportSchedule'])->name('export');
        });

        // Legacy defense scheduling (backward compatibility)
        Route::get('/schedule', [\App\Http\Controllers\Web\DefenseController::class, 'schedule'])
            ->middleware('role:admin_pfe')
            ->name('schedule');
        Route::post('/auto-schedule', [\App\Http\Controllers\Web\DefenseController::class, 'autoSchedule'])
            ->middleware('role:admin_pfe')
            ->name('auto-schedule');

        // Defense PV generation
        Route::post('/{defense}/pv', [\App\Http\Controllers\Web\DefenseController::class, 'generatePV'])
            ->middleware('role:admin_pfe')
            ->name('generate-pv');
    });

    // =====================================================================
    // ADMINISTRATION
    // =====================================================================

    Route::prefix('admin')->middleware('role:admin_pfe|chef_master')->name('admin.')->group(function () {

        // User management
        Route::resource('users', \App\Http\Controllers\Web\AdminController::class)->names([
            'index' => 'users.index',
            'create' => 'users.create',
            'store' => 'users.store',
            'show' => 'users.show',
            'edit' => 'users.edit',
            'update' => 'users.update',
            'destroy' => 'users.destroy'
        ]);

        // User role management
        Route::get('/users/{user}/roles', [\App\Http\Controllers\Web\AdminController::class, 'editRoles'])->name('users.roles');
        Route::put('/users/{user}/roles', [\App\Http\Controllers\Web\AdminController::class, 'updateRoles'])->name('users.update-roles');

        // Rooms management
        Route::resource('rooms', \App\Http\Controllers\Web\RoomController::class);

        // Enhanced Conflict resolution
        Route::prefix('conflicts')->name('conflicts.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\ConflictController::class, 'index'])->name('index');
            Route::get('/{subject}', [\App\Http\Controllers\Web\ConflictController::class, 'show'])->name('show');
            Route::post('/{subject}/resolve', [\App\Http\Controllers\Web\ConflictController::class, 'resolve'])
                ->middleware('role:chef_master')
                ->name('resolve');
            Route::post('/bulk-resolve', [\App\Http\Controllers\Web\ConflictController::class, 'bulkResolve'])
                ->middleware('role:chef_master')
                ->name('bulk-resolve');
            Route::post('/auto-resolve', [\App\Http\Controllers\Web\ConflictController::class, 'autoResolve'])
                ->middleware('role:chef_master')
                ->name('auto-resolve');
        });

        // Enhanced Project assignments
        Route::prefix('assignments')->name('assignments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\ProjectAssignmentController::class, 'index'])->name('index');
            Route::get('/auto', [\App\Http\Controllers\Web\ProjectAssignmentController::class, 'showAutoAssignment'])->name('auto');
            Route::post('/auto/execute', [\App\Http\Controllers\Web\ProjectAssignmentController::class, 'executeAutoAssignment'])
                ->middleware('role:chef_master')
                ->name('auto.execute');
            Route::get('/results', [\App\Http\Controllers\Web\ProjectAssignmentController::class, 'showResults'])->name('results');
            Route::get('/manual', [\App\Http\Controllers\Web\ProjectAssignmentController::class, 'showManualAssignment'])->name('manual');
            Route::post('/manual/execute', [\App\Http\Controllers\Web\ProjectAssignmentController::class, 'executeManualAssignment'])
                ->middleware('role:chef_master')
                ->name('manual.execute');
            Route::get('/external', [\App\Http\Controllers\Web\ProjectAssignmentController::class, 'showExternalProjects'])->name('external');
            Route::post('/external/approve', [\App\Http\Controllers\Web\ProjectAssignmentController::class, 'approveExternalProject'])
                ->middleware('role:chef_master')
                ->name('external.approve');
            Route::post('/balance-workload', [\App\Http\Controllers\Web\ProjectAssignmentController::class, 'balanceSupervisorWorkload'])
                ->middleware('role:chef_master')
                ->name('balance-workload');
        });

        // System settings
        Route::get('/settings', [\App\Http\Controllers\Web\AdminController::class, 'settings'])
            ->middleware('role:admin_pfe')
            ->name('settings');
        Route::put('/settings', [\App\Http\Controllers\Web\AdminController::class, 'updateSettings'])
            ->middleware('role:admin_pfe')
            ->name('update-settings');

        // Enhanced Student Import Management
        Route::prefix('students')->name('students.')->group(function () {
            Route::prefix('import')->name('import.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Web\StudentImportController::class, 'index'])->name('index');
                Route::get('/template', [\App\Http\Controllers\Web\StudentImportController::class, 'downloadTemplate'])->name('template');
                Route::post('/preview', [\App\Http\Controllers\Web\StudentImportController::class, 'preview'])->name('preview');
                Route::post('/import', [\App\Http\Controllers\Web\StudentImportController::class, 'import'])->name('execute');
                Route::get('/results/{importId}', [\App\Http\Controllers\Web\StudentImportController::class, 'showResults'])->name('results');
                Route::get('/errors/{importId}/export', [\App\Http\Controllers\Web\StudentImportController::class, 'exportErrors'])->name('export-errors');
                Route::post('/validate-structure', [\App\Http\Controllers\Web\StudentImportController::class, 'validateStructure'])->name('validate-structure');
                Route::post('/bulk-operations', [\App\Http\Controllers\Web\StudentImportController::class, 'bulkOperations'])->name('bulk-operations');
            });
        });
    });

    // =====================================================================
    // TEACHER SPECIFIC ROUTES
    // =====================================================================

    Route::prefix('teacher')->middleware('role:teacher')->name('teacher.')->group(function () {

        // Teacher Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Web\TeacherDashboardController::class, 'index'])->name('dashboard');

        // Enhanced Subject Management for Teachers
        Route::prefix('subjects')->name('subjects.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\TeacherSubjectController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Web\TeacherSubjectController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Web\TeacherSubjectController::class, 'store'])->name('store');
            Route::get('/{subject}', [\App\Http\Controllers\Web\TeacherSubjectController::class, 'show'])->name('show');
            Route::get('/{subject}/edit', [\App\Http\Controllers\Web\TeacherSubjectController::class, 'edit'])->name('edit');
            Route::put('/{subject}', [\App\Http\Controllers\Web\TeacherSubjectController::class, 'update'])->name('update');
            Route::post('/{subject}/submit', [\App\Http\Controllers\Web\TeacherSubjectController::class, 'submitForValidation'])->name('submit');
            Route::post('/{subject}/clone', [\App\Http\Controllers\Web\TeacherSubjectController::class, 'clone'])->name('clone');
            Route::post('/{subject}/archive', [\App\Http\Controllers\Web\TeacherSubjectController::class, 'archive'])->name('archive');
            Route::get('/{subject}/interest', [\App\Http\Controllers\Web\TeacherSubjectController::class, 'trackInterest'])->name('interest');
        });

        // Project Supervision
        Route::prefix('supervision')->name('supervision.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\TeacherSupervisionController::class, 'index'])->name('index');
            Route::get('/projects/{project}', [\App\Http\Controllers\Web\TeacherSupervisionController::class, 'showProject'])->name('project');
            Route::get('/projects/{project}/communication', [\App\Http\Controllers\Web\TeacherSupervisionController::class, 'showCommunication'])->name('communication');
            Route::post('/projects/{project}/message', [\App\Http\Controllers\Web\TeacherSupervisionController::class, 'sendMessage'])->name('send-message');
            Route::post('/projects/{project}/meeting', [\App\Http\Controllers\Web\TeacherSupervisionController::class, 'scheduleMeeting'])->name('schedule-meeting');
            Route::post('/projects/{project}/feedback', [\App\Http\Controllers\Web\TeacherSupervisionController::class, 'provideFeedback'])->name('provide-feedback');
            Route::post('/projects/{project}/milestone', [\App\Http\Controllers\Web\TeacherSupervisionController::class, 'updateMilestone'])->name('update-milestone');
            Route::get('/reports', [\App\Http\Controllers\Web\TeacherSupervisionController::class, 'generateSupervisionReport'])->name('reports');
        });

        // Deliverable Reviews
        Route::prefix('deliverables')->name('deliverables.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\DeliverableReviewController::class, 'index'])->name('index');
            Route::get('/{deliverable}', [\App\Http\Controllers\Web\DeliverableReviewController::class, 'show'])->name('show');
            Route::post('/{deliverable}/review', [\App\Http\Controllers\Web\DeliverableReviewController::class, 'submitReview'])->name('submit-review');
            Route::get('/{deliverable}/download', [\App\Http\Controllers\Web\DeliverableReviewController::class, 'download'])->name('download');
            Route::post('/bulk-review', [\App\Http\Controllers\Web\DeliverableReviewController::class, 'bulkReview'])->name('bulk-review');
            Route::get('/analytics', [\App\Http\Controllers\Web\DeliverableReviewController::class, 'analytics'])->name('analytics');
            Route::get('/export/report', [\App\Http\Controllers\Web\DeliverableReviewController::class, 'exportReport'])->name('export-report');
        });

        // Defense Management for Teachers
        Route::prefix('defenses')->name('defenses.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\TeacherDefenseController::class, 'index'])->name('index');
            Route::get('/{defense}/evaluation', [\App\Http\Controllers\Web\TeacherDefenseController::class, 'showEvaluation'])->name('evaluation');
            Route::post('/{defense}/evaluation', [\App\Http\Controllers\Web\TeacherDefenseController::class, 'submitEvaluation'])->name('submit-evaluation');
            Route::get('/{defense}/preparation', [\App\Http\Controllers\Web\TeacherDefenseController::class, 'showPreparation'])->name('preparation');
            Route::post('/{defense}/attendance', [\App\Http\Controllers\Web\TeacherDefenseController::class, 'markAttendance'])->name('mark-attendance');
            Route::post('/{defense}/final-decision', [\App\Http\Controllers\Web\TeacherDefenseController::class, 'submitFinalDecision'])->name('final-decision');
            Route::get('/calendar', [\App\Http\Controllers\Web\TeacherDefenseController::class, 'calendar'])->name('calendar');
            Route::get('/export', [\App\Http\Controllers\Web\TeacherDefenseController::class, 'exportSchedule'])->name('export');
        });
    });

    // =====================================================================
    // STUDENT SPECIFIC ROUTES
    // =====================================================================

    Route::prefix('student')->middleware('role:student')->name('student.')->group(function () {

        // Student Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Web\StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/year-overview', [\App\Http\Controllers\Web\StudentDashboardController::class, 'yearOverview'])->name('year-overview');
        Route::get('/team-hub', [\App\Http\Controllers\Web\StudentDashboardController::class, 'teamHub'])->name('team-hub');
        Route::get('/project-progress', [\App\Http\Controllers\Web\StudentDashboardController::class, 'projectProgress'])->name('project-progress');
        Route::get('/defense-preparation', [\App\Http\Controllers\Web\StudentDashboardController::class, 'defensePreparation'])->name('defense-preparation');
        Route::get('/profile', [\App\Http\Controllers\Web\StudentDashboardController::class, 'profile'])->name('profile');

        // Enhanced Team Management for Students
        Route::prefix('teams')->name('teams.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\StudentTeamController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Web\StudentTeamController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Web\StudentTeamController::class, 'store'])->name('store');
            Route::get('/browse', [\App\Http\Controllers\Web\StudentTeamController::class, 'browse'])->name('browse');
            Route::get('/{team}', [\App\Http\Controllers\Web\StudentTeamController::class, 'show'])->name('show');
            Route::get('/{team}/edit', [\App\Http\Controllers\Web\StudentTeamController::class, 'edit'])->name('edit');
            Route::put('/{team}', [\App\Http\Controllers\Web\StudentTeamController::class, 'update'])->name('update');
            Route::post('/{team}/join', [\App\Http\Controllers\Web\StudentTeamController::class, 'joinTeam'])->name('join');
            Route::delete('/{team}/leave', [\App\Http\Controllers\Web\StudentTeamController::class, 'leaveTeam'])->name('leave');
            Route::post('/{team}/invite', [\App\Http\Controllers\Web\StudentTeamController::class, 'sendInvitation'])->name('invite');
            Route::post('/invitations/{invitation}/respond', [\App\Http\Controllers\Web\StudentTeamController::class, 'respondToInvitation'])->name('respond-invitation');
            Route::get('/my/team', [\App\Http\Controllers\Web\StudentTeamController::class, 'myTeam'])->name('my-team');
        });

        // Enhanced Subject Selection for Students
        Route::prefix('subjects')->name('subjects.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\StudentSubjectController::class, 'index'])->name('index');
            Route::get('/browse', [\App\Http\Controllers\Web\StudentSubjectController::class, 'browse'])->name('browse');
            Route::get('/{subject}', [\App\Http\Controllers\Web\StudentSubjectController::class, 'show'])->name('show');
            Route::get('/preferences/set', [\App\Http\Controllers\Web\StudentSubjectController::class, 'showPreferences'])->name('preferences');
            Route::post('/preferences', [\App\Http\Controllers\Web\StudentSubjectController::class, 'setPreferences'])->name('set-preferences');
            Route::get('/competition/analysis', [\App\Http\Controllers\Web\StudentSubjectController::class, 'competitionAnalysis'])->name('competition-analysis');
            Route::post('/{subject}/favorite', [\App\Http\Controllers\Web\StudentSubjectController::class, 'toggleFavorite'])->name('toggle-favorite');
            Route::post('/external-proposal', [\App\Http\Controllers\Web\StudentSubjectController::class, 'proposeExternalSubject'])->name('propose-external');
        });

        // Enhanced Project Management for Students
        Route::prefix('projects')->name('projects.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\StudentProjectController::class, 'index'])->name('index');
            Route::get('/{project}', [\App\Http\Controllers\Web\StudentProjectController::class, 'show'])->name('show');
            Route::get('/{project}/progress', [\App\Http\Controllers\Web\StudentProjectController::class, 'showProgress'])->name('progress');
            Route::get('/{project}/deliverables', [\App\Http\Controllers\Web\StudentProjectController::class, 'deliverables'])->name('deliverables');
            Route::post('/{project}/deliverables', [\App\Http\Controllers\Web\StudentProjectController::class, 'uploadDeliverable'])->name('upload-deliverable');
            Route::get('/deliverables/{deliverable}/edit', [\App\Http\Controllers\Web\StudentProjectController::class, 'editDeliverable'])->name('edit-deliverable');
            Route::put('/deliverables/{deliverable}', [\App\Http\Controllers\Web\StudentProjectController::class, 'updateDeliverable'])->name('update-deliverable');
            Route::get('/{project}/timeline', [\App\Http\Controllers\Web\StudentProjectController::class, 'timeline'])->name('timeline');
            Route::post('/{project}/milestone', [\App\Http\Controllers\Web\StudentProjectController::class, 'updateMilestone'])->name('update-milestone');
            Route::get('/{project}/communication', [\App\Http\Controllers\Web\StudentProjectController::class, 'communication'])->name('communication');
            Route::post('/{project}/message', [\App\Http\Controllers\Web\StudentProjectController::class, 'sendMessage'])->name('send-message');
            Route::get('/my/project', [\App\Http\Controllers\Web\StudentProjectController::class, 'myProject'])->name('my-project');
        });

        // Enhanced Defense Management for Students
        Route::prefix('defense')->name('defense.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\StudentDefenseController::class, 'index'])->name('index');
            Route::get('/{project}/preparation', [\App\Http\Controllers\Web\StudentDefenseController::class, 'preparation'])->name('preparation');
            Route::post('/{project}/preparation', [\App\Http\Controllers\Web\StudentDefenseController::class, 'updatePreparation'])->name('update-preparation');
            Route::post('/{project}/presentation', [\App\Http\Controllers\Web\StudentDefenseController::class, 'uploadPresentation'])->name('upload-presentation');
            Route::post('/{project}/demo', [\App\Http\Controllers\Web\StudentDefenseController::class, 'uploadDemo'])->name('upload-demo');
            Route::post('/{project}/practice', [\App\Http\Controllers\Web\StudentDefenseController::class, 'schedule练习'])->name('schedule-practice');
            Route::get('/{project}/assessment', [\App\Http\Controllers\Web\StudentDefenseController::class, 'readinessAssessment'])->name('assessment');
            Route::post('/{project}/readiness', [\App\Http\Controllers\Web\StudentDefenseController::class, 'submitReadiness'])->name('submit-readiness');
            Route::get('/show/{defense}', [\App\Http\Controllers\Web\StudentDefenseController::class, 'viewDefense'])->name('show');
        });
    });

    // =====================================================================
    // REPORTS & ANALYTICS
    // =====================================================================

    Route::prefix('reports')->middleware('role:admin_pfe|chef_master|teacher')->name('reports.')->group(function () {

        // Report dashboard
        Route::get('/', [\App\Http\Controllers\Web\ReportController::class, 'index'])->name('index');

        // Defense reports
        Route::get('/defenses', [\App\Http\Controllers\Web\ReportController::class, 'defenses'])->name('defenses');
        Route::get('/defense-schedule', [\App\Http\Controllers\Web\ReportController::class, 'defenseSchedule'])->name('defense-schedule');

        // Project reports
        Route::get('/projects', [\App\Http\Controllers\Web\ReportController::class, 'projects'])->name('projects');
        Route::get('/project-progress', [\App\Http\Controllers\Web\ReportController::class, 'projectProgress'])->name('project-progress');

        // Team reports
        Route::get('/teams', [\App\Http\Controllers\Web\ReportController::class, 'teams'])->name('teams');
        Route::get('/team-performance', [\App\Http\Controllers\Web\ReportController::class, 'teamPerformance'])->name('team-performance');

        // Subject reports
        Route::get('/subjects', [\App\Http\Controllers\Web\ReportController::class, 'subjects'])->name('subjects');
        Route::get('/subject-analysis', [\App\Http\Controllers\Web\ReportController::class, 'subjectAnalysis'])->name('subject-analysis');

        // Statistics
        Route::get('/statistics', [\App\Http\Controllers\Web\ReportController::class, 'statistics'])->name('statistics');

        // Export routes
        Route::get('/{report}/export', [\App\Http\Controllers\Web\ReportController::class, 'export'])
            ->where('report', '[a-zA-Z0-9\-_]+')
            ->name('export');
    });

    // =====================================================================
    // NOTIFICATIONS
    // =====================================================================

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\NotificationController::class, 'index'])->name('index');
        Route::put('/{notification}/read', [\App\Http\Controllers\Web\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [\App\Http\Controllers\Web\NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{notification}', [\App\Http\Controllers\Web\NotificationController::class, 'destroy'])->name('destroy');

        // Notification preferences
        Route::get('/preferences', [\App\Http\Controllers\Web\NotificationController::class, 'preferences'])->name('preferences');
        Route::put('/preferences', [\App\Http\Controllers\Web\NotificationController::class, 'updatePreferences'])->name('update-preferences');
    });

    // =====================================================================
    // SEARCH
    // =====================================================================

    Route::get('/search', [\App\Http\Controllers\Web\SearchController::class, 'index'])->name('search');

    // =====================================================================
    // PROFILE MANAGEMENT
    // =====================================================================

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [\App\Http\Controllers\Web\ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [\App\Http\Controllers\Web\ProfileController::class, 'update'])->name('update');
        Route::post('/avatar', [\App\Http\Controllers\Web\ProfileController::class, 'uploadAvatar'])->name('avatar');
    });

});

// =========================================================================
// FALLBACK ROUTES
// =========================================================================

// Catch-all route for SPA-like behavior (optional)
// Route::fallback(function () {
//     return view('errors.404');
// });

// =========================================================================
// PASSWORD RESET ROUTES (if implementing custom password reset)
// =========================================================================

// Route::middleware('guest')->group(function () {
//     Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
//     Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
//     Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
//     Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
// });

// =========================================================================
// EMAIL VERIFICATION ROUTES (if implementing email verification)
// =========================================================================

// Route::middleware('auth')->group(function () {
//     Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
//     Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
//         ->middleware(['signed', 'throttle:6,1'])
//         ->name('verification.verify');
//     Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
//         ->middleware('throttle:6,1')
//         ->name('verification.send');
// });