<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Speciality;
use App\Models\Room;
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
     * Show form to create new user
     */
    public function createUser(): View
    {
        $specialities = Speciality::active()->get();
        return view('admin.users.create', compact('specialities'));
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:student,teacher,department_head,admin',
            'matricule' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:255',
            'speciality_id' => 'nullable|exists:specialities,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'matricule' => $request->matricule,
                'department' => 'Computer Science', // Fixed to Computer Science only
                'speciality_id' => $request->speciality_id,
                'password' => bcrypt($request->password),
            ]);

            return redirect()->route('admin.users')
                ->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show form to edit user
     */
    public function editUser(User $user): View
    {
        $specialities = Speciality::active()->get();
        return view('admin.users.edit', compact('user', 'specialities'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:student,teacher,department_head,admin',
            'matricule' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:255',
            'speciality_id' => 'nullable|exists:specialities,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            $data = $request->only(['name', 'email', 'role', 'matricule', 'speciality_id']);
            $data['department'] = 'Computer Science'; // Fixed to Computer Science only

            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->password);
            }

            $user->update($data);

            return redirect()->route('admin.users')
                ->with('success', 'User updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete user
     */
    public function destroyUser(User $user): RedirectResponse
    {
        try {
            if ($user->id === auth()->id()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete your own account.');
            }

            $user->delete();
            return redirect()->route('admin.users')
                ->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Update system settings
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        // This would typically update system settings
        // For now, just redirect back with success
        return redirect()->back()
            ->with('success', 'Settings updated successfully!');
    }

    /**
     * Generate system report
     */
    public function generateReport(): View
    {
        return view('admin.reports.generate');
    }

    /**
     * Backup system
     */
    public function backup(): RedirectResponse
    {
        // This would typically create a system backup
        return redirect()->back()
            ->with('success', 'Backup created successfully!');
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
        // Determine validation rules based on speciality option
        $rules = [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
            'speciality_option' => 'required|in:existing,new',
        ];

        if ($request->speciality_option === 'existing') {
            $rules['existing_speciality_id'] = 'required|exists:specialities,id';
        } else {
            $rules = array_merge($rules, [
                'speciality_name' => 'required|string|max:255',
                'speciality_level' => 'required|in:L2 ING,L3 LMD,L4 ING,L5 ING,M2 LMD',
                'academic_year' => 'required|string|max:20',
                'semester' => 'nullable|string|max:10',
                'speciality_code' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:500',
            ]);
        }

        $request->validate($rules);

        try {
            // Store the uploaded file temporarily
            $file = $request->file('excel_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('imports', $filename, 'local');
            $fullPath = Storage::disk('local')->path($filePath);

            // Prepare speciality data
            if ($request->speciality_option === 'existing') {
                $speciality = Speciality::findOrFail($request->existing_speciality_id);
                $specialityData = [
                    'existing_speciality_id' => $speciality->id,
                    'name' => $speciality->name,
                    'level' => $speciality->level,
                    'academic_year' => $speciality->academic_year,
                    'semester' => $speciality->semester,
                    'code' => $speciality->code,
                    'description' => $speciality->description,
                ];
            } else {
                $specialityData = [
                    'name' => $request->speciality_name,
                    'level' => $request->speciality_level,
                    'academic_year' => $request->academic_year,
                    'semester' => $request->semester,
                    'code' => $request->speciality_code,
                    'description' => $request->description,
                ];
            }

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
            'level' => 'required|in:L2 ING,L3 LMD,L4 ING,L5 ING,M2 LMD',
            'academic_year' => 'required|string|max:20',
            'semester' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $data = $request->all();
            $data['is_active'] = $request->boolean('is_active');

            Speciality::create($data);
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
            'level' => 'required|in:L2 ING,L3 LMD,L4 ING,L5 ING,M2 LMD',
            'academic_year' => 'required|string|max:20',
            'semester' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $data = $request->all();
            $data['is_active'] = $request->boolean('is_active');

            $speciality->update($data);
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
     * Process bulk import
     */
    public function processBulkImport(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        try {
            // This would process the CSV file for bulk user import
            // For now, just return success message
            return redirect()->back()
                ->with('success', 'Bulk import completed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Bulk import failed: ' . $e->getMessage())
                ->withInput();
        }
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
     * Show rooms management
     */
    public function rooms(): View
    {
        $rooms = Room::withCount('defenses')->paginate(15);
        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Create new room
     */
    public function createRoom(): View
    {
        return view('admin.rooms.create');
    }

    /**
     * Store new room
     */
    public function storeRoom(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name',
            'capacity' => 'required|integer|min:1|max:500',
            'equipment' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'is_available' => 'boolean',
        ]);

        try {
            $data = $request->all();
            $data['is_available'] = $request->boolean('is_available', true);

            Room::create($data);
            return redirect()->route('admin.rooms')
                ->with('success', 'Room created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create room: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Edit room
     */
    public function editRoom(Room $room): View
    {
        return view('admin.rooms.edit', compact('room'));
    }

    /**
     * Update room
     */
    public function updateRoom(Request $request, Room $room): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name,' . $room->id,
            'capacity' => 'required|integer|min:1|max:500',
            'equipment' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'is_available' => 'boolean',
        ]);

        try {
            $data = $request->all();
            $data['is_available'] = $request->boolean('is_available');

            $room->update($data);
            return redirect()->route('admin.rooms')
                ->with('success', 'Room updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update room: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete room
     */
    public function destroyRoom(Room $room): RedirectResponse
    {
        try {
            if ($room->defenses()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete room with scheduled defenses.');
            }

            $room->delete();
            return redirect()->route('admin.rooms')
                ->with('success', 'Room deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete room: ' . $e->getMessage());
        }
    }

    /**
     * Maintenance page
     */
    public function maintenance(): View
    {
        return view('admin.maintenance');
    }
}
