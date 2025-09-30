<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Speciality;
use App\Services\StudentImportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected StudentImportService $importService;

    public function __construct(StudentImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Display users management page
     */
    public function users(): View
    {
        $users = User::with('speciality')->paginate(20);
        $specialities = Speciality::active()->get();

        return view('admin.users.index', compact('users', 'specialities'));
    }

    /**
     * Show student upload form
     */
    public function studentsUpload(): View
    {
        $specialities = Speciality::active()->get();
        return view('admin.students.upload', compact('specialities'));
    }

    /**
     * Process student Excel upload
     */
    public function studentsUploadProcess(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
            'speciality_name' => 'required|string|max:255',
            'speciality_level' => 'required|in:license,master,doctorate',
            'academic_year' => 'required|string|max:20',
            'semester' => 'nullable|string|max:10',
            'speciality_code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            // Store the uploaded file temporarily
            $file = $request->file('excel_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('imports', $filename, 'local');
            $fullPath = Storage::disk('local')->path($filePath);

            // Prepare speciality data
            $specialityData = [
                'name' => $request->speciality_name,
                'level' => $request->speciality_level,
                'academic_year' => $request->academic_year,
                'semester' => $request->semester,
                'code' => $request->speciality_code,
                'description' => $request->description,
            ];

            // Process the import
            $result = $this->importService->importFromExcel($fullPath, $specialityData);

            // Clean up temporary file
            Storage::disk('local')->delete($filePath);

            if ($result['success']) {
                $data = $result['data'];
                $message = "Import completed successfully! " .
                    "Created: {$data['created']}, " .
                    "Updated: {$data['updated']}, " .
                    "Skipped: {$data['skipped']} students.";

                if (!empty($data['errors'])) {
                    $message .= " Warning: " . count($data['errors']) . " errors occurred.";
                }

                return redirect()->back()
                    ->with('success', $message)
                    ->with('import_details', $data);
            } else {
                return redirect()->back()
                    ->with('error', $result['message'])
                    ->withInput();
            }

        } catch (\Exception $e) {
            Log::error('Student upload error: ' . $e->getMessage());

            // Clean up file if it exists
            if (isset($filePath)) {
                Storage::disk('local')->delete($filePath);
            }

            return redirect()->back()
                ->with('error', 'Upload failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show specialities management
     */
    public function specialities(): View
    {
        $specialities = Speciality::withCount('students')->paginate(15);
        return view('admin.specialities.index', compact('specialities'));
    }

    /**
     * Create new speciality
     */
    public function createSpeciality(): View
    {
        return view('admin.specialities.create');
    }

    /**
     * Store new speciality
     */
    public function storeSpeciality(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'level' => 'required|in:license,master,doctorate',
            'academic_year' => 'required|string|max:20',
            'semester' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            Speciality::create($request->all());
            return redirect()->route('admin.specialities')
                ->with('success', 'Speciality created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create speciality: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Edit speciality
     */
    public function editSpeciality(Speciality $speciality): View
    {
        return view('admin.specialities.edit', compact('speciality'));
    }

    /**
     * Update speciality
     */
    public function updateSpeciality(Request $request, Speciality $speciality): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'level' => 'required|in:license,master,doctorate',
            'academic_year' => 'required|string|max:20',
            'semester' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        try {
            $speciality->update($request->all());
            return redirect()->route('admin.specialities')
                ->with('success', 'Speciality updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update speciality: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete speciality
     */
    public function destroySpeciality(Speciality $speciality): RedirectResponse
    {
        try {
            if ($speciality->students()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete speciality with assigned students.');
            }

            $speciality->delete();
            return redirect()->route('admin.specialities')
                ->with('success', 'Speciality deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete speciality: ' . $e->getMessage());
        }
    }

    /**
     * System settings page
     */
    public function settings(): View
    {
        return view('admin.settings');
    }

    /**
     * Reports page
     */
    public function reports(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_teachers' => User::where('role', 'teacher')->count(),
            'total_specialities' => Speciality::count(),
            'active_specialities' => Speciality::where('is_active', true)->count(),
        ];

        return view('admin.reports', compact('stats'));
    }

    /**
     * Analytics page
     */
    public function analytics(): View
    {
        return view('admin.analytics');
    }

    /**
     * System logs
     */
    public function logs(): View
    {
        return view('admin.logs');
    }

    /**
     * Bulk import users
     */
    public function bulkImport(): View
    {
        return view('admin.users.bulk-import');
    }

    /**
     * Subjects report
     */
    public function subjectsReport(): View
    {
        $stats = [
            'total' => \App\Models\Subject::count(),
            'validated' => \App\Models\Subject::where('status', 'validated')->count(),
            'pending' => \App\Models\Subject::where('status', 'pending_validation')->count(),
            'rejected' => \App\Models\Subject::where('status', 'rejected')->count(),
        ];

        return view('admin.reports.subjects', compact('stats'));
    }

    /**
     * Teams report
     */
    public function teamsReport(): View
    {
        $stats = [
            'total' => \App\Models\Team::count(),
            'active' => \App\Models\Team::where('status', 'active')->count(),
            'forming' => \App\Models\Team::where('status', 'forming')->count(),
        ];

        return view('admin.reports.teams', compact('stats'));
    }

    /**
     * Projects report
     */
    public function projectsReport(): View
    {
        $stats = [
            'total' => \App\Models\Project::count(),
            'active' => \App\Models\Project::where('status', 'active')->count(),
            'completed' => \App\Models\Project::where('status', 'completed')->count(),
        ];

        return view('admin.reports.projects', compact('stats'));
    }

    /**
     * Defenses report
     */
    public function defensesReport(): View
    {
        $stats = [
            'total' => \App\Models\Defense::count(),
            'scheduled' => \App\Models\Defense::where('status', 'scheduled')->count(),
            'completed' => \App\Models\Defense::where('status', 'completed')->count(),
        ];

        return view('admin.reports.defenses', compact('stats'));
    }

    /**
     * Maintenance page
     */
    public function maintenance(): View
    {
        return view('admin.maintenance');
    }
}
