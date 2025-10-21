<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\SubjectController;
use App\Http\Controllers\Web\TeamController;
use App\Http\Controllers\Web\ProjectController;
use App\Http\Controllers\Web\DefenseController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\GradeController;
use App\Http\Controllers\Web\SubjectPreferenceController;
use App\Http\Controllers\Web\AllocationController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Admin\StudentUploadController;
use App\Http\Controllers\Admin\AllocationController as AdminAllocationController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentSetupController;
use App\Http\Controllers\SpecialityController;
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

// Language switching routes
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Contact routes
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// About route
Route::get('/about', [ContactController::class, 'about'])->name('about');

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
    // STUDENT SETUP WIZARD (no middleware needed - handled by StudentProfileSetup middleware)
    // =====================================================================

    Route::prefix('student/setup')->name('student.setup.')->group(function () {
        Route::get('/welcome', [StudentSetupController::class, 'welcome'])->name('welcome');
        Route::get('/personal-info', [StudentSetupController::class, 'personalInfo'])->name('personal-info');
        Route::post('/personal-info', [StudentSetupController::class, 'storePersonalInfo'])->name('store-personal-info');
        Route::get('/marks', [StudentSetupController::class, 'marks'])->name('marks');
        Route::post('/marks', [StudentSetupController::class, 'storeMarks'])->name('store-marks');
        Route::get('/complete', [StudentSetupController::class, 'complete'])->name('complete');
        Route::post('/finish', [StudentSetupController::class, 'finish'])->name('finish');
    });

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

    Route::prefix('subjects')->name('subjects.')->middleware('current_year_data')->group(function () {
        // Static routes first
        Route::get('/', [SubjectController::class, 'index'])->name('index');
        Route::get('/available/{grade?}', [SubjectController::class, 'available'])->name('available');

        // Teachers and students can create subjects
        Route::middleware('role:teacher,student')->group(function () {
            Route::get('/create', [SubjectController::class, 'create'])->name('create');
            Route::post('/', [SubjectController::class, 'store'])->name('store');
        });

        // Dynamic route for modal content
        Route::get('/{subject}/modal', [SubjectController::class, 'modal'])->name('modal');

        // Dynamic route for team requests
        Route::get('/{subject}/requests', [SubjectController::class, 'requests'])->name('requests');

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

    Route::prefix('teams')->name('teams.')->middleware('current_year_data')->group(function () {
        // All users can view teams
        Route::get('/', [TeamController::class, 'index'])->name('index');

        // Students can create and manage teams (static routes before dynamic ones)
        Route::middleware('role:student')->group(function () {
            Route::get('/my-team', [TeamController::class, 'myTeam'])->name('my-team');
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

            // Subject preferences management (up to 10 subjects)
            Route::get('/{team}/subject-preferences', [TeamController::class, 'subjectPreferences'])->name('subject-preferences');
            Route::post('/{team}/subject-preferences', [TeamController::class, 'addSubjectPreference'])->name('add-subject-preference');
            Route::delete('/{team}/subject-preferences/{subject}', [TeamController::class, 'removeSubjectPreference'])->name('remove-subject-preference');
            Route::put('/{team}/subject-preferences/order', [TeamController::class, 'updatePreferenceOrder'])->name('update-preference-order');

            // Subject requests management
            Route::get('/{team}/subject-requests', [TeamController::class, 'subjectRequests'])->name('subject-requests');
            Route::post('/{team}/request-subject', [TeamController::class, 'requestSubject'])->name('request-subject');
            Route::put('/{team}/subject-requests/order', [TeamController::class, 'updateSubjectRequestOrder'])->name('update-subject-request-order');
            Route::delete('/{team}/subject-requests/{subjectRequest}', [TeamController::class, 'cancelSubjectRequest'])->name('cancel-subject-request');

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

    Route::prefix('projects')->name('projects.')->middleware('current_year_data')->group(function () {
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

    Route::prefix('defenses')->name('defenses.')->middleware('current_year_data')->group(function () {
        // Teachers, department heads, and admins can view defenses
        Route::middleware('role:teacher,department_head,admin')->group(function () {
            Route::get('/', [DefenseController::class, 'index'])->name('index');
            Route::get('/calendar', [DefenseController::class, 'calendar'])->name('calendar');
        });

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
            Route::get('/schedule-defense', [DefenseController::class, 'scheduleForm'])->name('schedule-form');
            Route::post('/schedule-defense', [DefenseController::class, 'schedule'])->name('schedule');
            Route::post('/auto-schedule', [DefenseController::class, 'autoSchedule'])->name('auto-schedule');
            Route::post('/{defense}/grade', [DefenseController::class, 'submitGrade'])->name('submit-grade');
            Route::get('/{defense}/edit', [DefenseController::class, 'edit'])->name('edit');
            Route::put('/{defense}', [DefenseController::class, 'update'])->name('update');
            Route::delete('/{defense}/cancel', [DefenseController::class, 'cancel'])->name('cancel');
            Route::delete('/{defense}', [DefenseController::class, 'destroy'])->name('destroy');
            Route::post('/{defense}/complete', [DefenseController::class, 'complete'])->name('complete');
            Route::post('/{defense}/add-pv-notes', [DefenseController::class, 'addPvNotes'])->name('add-pv-notes');
            Route::get('/{defense}/report', [DefenseController::class, 'generateReport'])->name('generate-report');
            Route::get('/{defense}/report/pdf', [DefenseController::class, 'downloadReportPdf'])->name('download-report-pdf');
            Route::get('/{defense}/student/{student}/report/pdf', [DefenseController::class, 'downloadStudentReportPdf'])->name('download-student-report-pdf');
            Route::get('/{defense}/batch-reports/pdf', [DefenseController::class, 'downloadBatchStudentReports'])->name('download-batch-reports-pdf');
        });

        // Dynamic route for viewing specific defenses (must be at the end)
        Route::get('/{defense}', [DefenseController::class, 'show'])->name('show');
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
        Route::get('/users/{user}/details', [AdminController::class, 'detailsUser'])->name('users.details');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
        Route::get('/users/bulk-import', [AdminController::class, 'bulkImport'])->name('users.bulk-import');
        Route::post('/users/bulk-import', [AdminController::class, 'processBulkImport'])->name('users.bulk-import.process');

        // Student Management - Excel Import
        Route::get('/students/upload', [StudentUploadController::class, 'showUploadForm'])->name('students.upload');
        Route::post('/students/upload', [StudentUploadController::class, 'upload'])->name('students.upload.process');
        Route::get('/students/template', [StudentUploadController::class, 'downloadTemplate'])->name('students.template');
        Route::get('/students/import-history', [StudentUploadController::class, 'importHistory'])->name('students.import-history');

        // Speciality Management
        Route::resource('specialities', \App\Http\Controllers\Admin\SpecialityController::class);
        Route::patch('/specialities/{speciality}/toggle-status', [\App\Http\Controllers\Admin\SpecialityController::class, 'toggleStatus'])->name('specialities.toggle-status');
        Route::get('/specialities/api/current-year', [\App\Http\Controllers\Admin\SpecialityController::class, 'getCurrentAcademicYear'])->name('specialities.current-year');

        // Allocation Management
        Route::prefix('allocations')->name('allocations.')->group(function () {
            Route::get('/', [AdminAllocationController::class, 'index'])->name('index');
            Route::get('/{deadline}', [AdminAllocationController::class, 'show'])->name('show');
            Route::post('/{deadline}/auto-allocation', [AdminAllocationController::class, 'performAutoAllocation'])->name('auto-allocation');
            Route::post('/manual-assignment', [AdminAllocationController::class, 'manualAssignment'])->name('manual-assignment');
            Route::post('/{deadline}/second-round', [AdminAllocationController::class, 'initializeSecondRound'])->name('second-round');
            Route::delete('/allocation/{allocation}', [AdminAllocationController::class, 'removeAllocation'])->name('remove-allocation');
        });

        // Student Marks Management
        Route::get('/marks', [AdminController::class, 'marks'])->name('marks');
        Route::get('/marks/create', [AdminController::class, 'createMark'])->name('marks.create');
        Route::post('/marks', [AdminController::class, 'storeMark'])->name('marks.store');
        Route::get('/marks/{mark}/edit', [AdminController::class, 'editMark'])->name('marks.edit');
        Route::put('/marks/{mark}', [AdminController::class, 'updateMark'])->name('marks.update');
        Route::delete('/marks/{mark}', [AdminController::class, 'destroyMark'])->name('marks.destroy');
        Route::get('/marks/bulk-import', [AdminController::class, 'bulkImportMarks'])->name('marks.bulk-import');
        Route::post('/marks/bulk-import', [AdminController::class, 'processBulkImportMarks'])->name('marks.bulk-import.process');

        // Subject Approval Management
        Route::get('/subjects/pending', [AdminController::class, 'pendingSubjects'])->name('subjects.pending');
        Route::post('/subjects/{subject}/approve', [AdminController::class, 'approveSubject'])->name('subjects.approve');
        Route::post('/subjects/{subject}/reject', [AdminController::class, 'rejectSubject'])->name('subjects.reject');
        Route::get('/subjects/all', [AdminController::class, 'allSubjects'])->name('subjects.all');

        // Subject Request Management
        Route::get('/subject-requests', [AdminController::class, 'subjectRequests'])->name('subject-requests');
        Route::post('/subject-requests/{subjectRequest}/approve', [AdminController::class, 'approveSubjectRequest'])->name('subject-requests.approve');
        Route::post('/subject-requests/{subjectRequest}/reject', [AdminController::class, 'rejectSubjectRequest'])->name('subject-requests.reject');

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

    // =====================================================================
    // GRADES MANAGEMENT ROUTES
    // =====================================================================

    Route::prefix('grades')->name('grades.')->group(function () {
        // Students can manage their grades
        Route::middleware('role:student')->group(function () {
            Route::get('/', [GradeController::class, 'index'])->name('index');
            Route::get('/create', [GradeController::class, 'create'])->name('create');
            Route::post('/', [GradeController::class, 'store'])->name('store');
            Route::get('/{grade}/edit', [GradeController::class, 'edit'])->name('edit');
            Route::put('/{grade}', [GradeController::class, 'update'])->name('update');
            Route::post('/{grade}/submit', [GradeController::class, 'submitForVerification'])->name('submit');
            Route::delete('/{grade}', [GradeController::class, 'destroy'])->name('destroy');
        });

        // Admins and department heads can verify grades
        Route::middleware('role:admin,department_head')->group(function () {
            Route::get('/pending', [GradeController::class, 'pendingVerification'])->name('pending');
            Route::post('/{grade}/verify', [GradeController::class, 'verify'])->name('verify');
            Route::post('/{grade}/reject', [GradeController::class, 'reject'])->name('reject');
            Route::post('/batch-verify', [GradeController::class, 'batchVerify'])->name('batch-verify');
        });

        Route::get('/{grade}', [GradeController::class, 'show'])->name('show');
    });

    // =====================================================================
    // SUBJECT REQUESTS ROUTES
    // =====================================================================

    Route::get('/subject-requests', [TeamController::class, 'allSubjectRequests'])->name('subject-requests.all');

    // =====================================================================
    // SUBJECT PREFERENCES ROUTES
    // =====================================================================

    Route::prefix('preferences')->name('preferences.')->middleware('role:student')->group(function () {
        Route::get('/', [SubjectPreferenceController::class, 'index'])->name('index');
        Route::get('/create', [SubjectPreferenceController::class, 'create'])->name('create');
        Route::post('/', [SubjectPreferenceController::class, 'store'])->name('store');
        Route::put('/', [SubjectPreferenceController::class, 'update'])->name('update');
        Route::delete('/', [SubjectPreferenceController::class, 'destroy'])->name('destroy');
        Route::post('/submit', [SubjectPreferenceController::class, 'submit'])->name('submit');
    });

    // =====================================================================
    // ALLOCATION MANAGEMENT ROUTES
    // =====================================================================

    Route::prefix('allocations')->name('allocations.')->group(function () {
        // Students can view their allocation
        Route::middleware('role:student')->group(function () {
            Route::get('/my-allocation', [AllocationController::class, 'myAllocation'])->name('my-allocation');
        });

        // Admins and department heads can manage allocations
        Route::middleware('role:admin,department_head')->group(function () {
            Route::get('/', [AllocationController::class, 'index'])->name('index');
            Route::get('/deadlines', [AllocationController::class, 'deadlines'])->name('deadlines');
            Route::post('/deadlines', [AllocationController::class, 'storeDeadline'])->name('deadlines.store');
            Route::put('/deadlines/{deadline}', [AllocationController::class, 'updateDeadline'])->name('deadlines.update');
            Route::post('/run-allocation', [AllocationController::class, 'runAllocation'])->name('run-allocation');
            Route::get('/results', [AllocationController::class, 'results'])->name('results');
            Route::post('/{allocation}/confirm', [AllocationController::class, 'confirm'])->name('confirm');
            Route::post('/{allocation}/reject', [AllocationController::class, 'reject'])->name('reject');
        });
    });

    // =====================================================================
    // SPECIALITY MANAGEMENT ROUTES
    // =====================================================================

    Route::prefix('specialities')->name('specialities.')->group(function () {
        // All authenticated users can view specialities
        Route::get('/', [SpecialityController::class, 'index'])->name('index');
        Route::get('/{speciality}', [SpecialityController::class, 'show'])->name('show');

        // Admins and department heads can manage specialities
        Route::middleware('role:admin,department_head')->group(function () {
            Route::get('/create', [SpecialityController::class, 'create'])->name('create');
            Route::post('/', [SpecialityController::class, 'store'])->name('store');
            Route::get('/{speciality}/edit', [SpecialityController::class, 'edit'])->name('edit');
            Route::put('/{speciality}', [SpecialityController::class, 'update'])->name('update');
            Route::delete('/{speciality}', [SpecialityController::class, 'destroy'])->name('destroy');
            Route::patch('/{speciality}/toggle-active', [SpecialityController::class, 'toggleActive'])->name('toggleActive');
        });
    });
});

// =========================================================================
// ADMIN ROUTES
// =========================================================================

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::middleware('role:admin')->group(function () {
        // User management
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
        Route::get('/users/bulk-import', [AdminController::class, 'bulkImport'])->name('users.bulk-import');
        Route::post('/users/bulk-import', [AdminController::class, 'processBulkImport'])->name('users.bulk-import.process');

        // Birth certificate management
        Route::get('/birth-certificates', [AdminController::class, 'birthCertificates'])->name('birth-certificates');
        Route::post('/birth-certificates/{user}/approve', [AdminController::class, 'approveBirthCertificate'])->name('birth-certificates.approve');
        Route::post('/birth-certificates/{user}/reject', [AdminController::class, 'rejectBirthCertificate'])->name('birth-certificates.reject');

        // Student marks management
        Route::get('/marks', [AdminController::class, 'marks'])->name('marks');
        Route::get('/marks/create', [AdminController::class, 'createMark'])->name('marks.create');
        Route::post('/marks', [AdminController::class, 'storeMark'])->name('marks.store');
        Route::get('/marks/{mark}/edit', [AdminController::class, 'editMark'])->name('marks.edit');
        Route::put('/marks/{mark}', [AdminController::class, 'updateMark'])->name('marks.update');
        Route::delete('/marks/{mark}', [AdminController::class, 'destroyMark'])->name('marks.destroy');

        // Bulk marks management
        Route::get('/marks/bulk-create', [AdminController::class, 'bulkMarksCreate'])->name('marks.bulk-create');
        Route::post('/marks/bulk-store', [AdminController::class, 'bulkMarksStore'])->name('marks.bulk-store');

        // Bulk marks for all students
        Route::get('/marks/bulk-all-students', [AdminController::class, 'bulkAllStudentsCreate'])->name('marks.bulk-all-create');
        Route::post('/marks/bulk-all-store', [AdminController::class, 'bulkAllStudentsStore'])->name('marks.bulk-all-store');

        Route::get('/students/{user}/marks/{semester}', [AdminController::class, 'studentMarksBySemester'])->name('students.marks.semester');
        Route::get('/students/{user}/marks-summary', [AdminController::class, 'studentMarksSummary'])->name('students.marks.summary');

        // Academic Year Management
        Route::prefix('academic-years')->name('academic-years.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AcademicYearController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\AcademicYearController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\AcademicYearController::class, 'store'])->name('store');
            Route::get('/{academicYear}', [\App\Http\Controllers\Admin\AcademicYearController::class, 'show'])->name('show');
            Route::get('/{academicYear}/edit', [\App\Http\Controllers\Admin\AcademicYearController::class, 'edit'])->name('edit');
            Route::put('/{academicYear}', [\App\Http\Controllers\Admin\AcademicYearController::class, 'update'])->name('update');
            Route::post('/{academicYear}/activate', [\App\Http\Controllers\Admin\AcademicYearController::class, 'activate'])->name('activate');
            Route::post('/{academicYear}/end', [\App\Http\Controllers\Admin\AcademicYearController::class, 'end'])->name('end');
            Route::delete('/{academicYear}', [\App\Http\Controllers\Admin\AcademicYearController::class, 'destroy'])->name('destroy');
            Route::get('/history/completed', [\App\Http\Controllers\Admin\AcademicYearController::class, 'history'])->name('history');
            Route::get('/{academicYear}/statistics', [\App\Http\Controllers\Admin\AcademicYearController::class, 'statistics'])->name('statistics');
        });

        // Student Alerts Management
        Route::get('/alerts', [AdminController::class, 'alerts'])->name('alerts');
        Route::get('/alerts/{alert}', [AdminController::class, 'showAlert'])->name('alerts.show');
        Route::post('/alerts/{alert}/respond', [AdminController::class, 'respondToAlert'])->name('alerts.respond');

        // Reports and analytics
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    });
});

// =========================================================================
// STUDENT ROUTES
// =========================================================================
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::post('/alert', [StudentController::class, 'storeAlert'])->name('alert.store');
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
