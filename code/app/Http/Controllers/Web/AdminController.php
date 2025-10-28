<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Speciality;
use App\Models\Room;
use App\Models\Subject;
use App\Models\Team;
use App\Models\StudentMark;
use App\Models\StudentAlert;
use App\Models\SubjectRequest;
use App\Models\AllocationDeadline;
use App\Services\StudentImportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
    public function users(Request $request)
    {
        $query = User::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Apply role filter (default to student)
        $selectedRole = $request->get('role', 'student');
        if ($selectedRole && $selectedRole !== 'all') {
            $query->where('role', $selectedRole);
        }

        // Apply speciality filter
        if ($request->filled('speciality_id')) {
            $query->where('speciality_id', $request->speciality_id);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Order by name and paginate
        $users = $query->orderBy('name')
                      ->with(['teamMember.team', 'speciality'])
                      ->paginate(20)
                      ->appends($request->all());

        $specialities = Speciality::active()->get();

        // return $users->last()->speciality;
        // Get counts for each role
        $roleCounts = [
            'all' => User::count(),
            'student' => User::where('role', 'student')->count(),
            'teacher' => User::where('role', 'teacher')->count(),
            'department_head' => User::where('role', 'department_head')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users.index', compact('users', 'specialities', 'roleCounts', 'selectedRole'));
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
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'grade' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
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
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'date_naissance' => $request->date_naissance,
                'lieu_naissance' => $request->lieu_naissance,
                'grade' => $request->grade,
                'position' => $request->position,
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
     * Show user details with marks
     */
    public function detailsUser(User $user): View
    {
        // Load the speciality relationship
        $user->load('speciality');

        $marks = StudentMark::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $students = User::where('role', 'student')
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();

        return view('admin.users.details', compact('user', 'marks', 'students'));
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
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'grade' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
        ]);
        //  dd($request->all());

        try {
            $data = $request->only(['name', 'email', 'role', 'matricule', 'speciality_id', 'first_name', 'last_name', 'date_naissance', 'lieu_naissance', 'grade', 'position']);
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
            return redirect()->route('admin.specialities.index')
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
            return redirect()->route('admin.specialities.index')
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
            return redirect()->route('admin.specialities.index')
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
        $universityInfo = \App\Models\Setting::getUniversityInfo();
        $currentLogo = \App\Models\Setting::getUniversityLogo();

        // Get all settings grouped by category
        $settingsGroups = \App\Services\SettingsService::getAllSettings();

        return view('admin.settings', compact('universityInfo', 'currentLogo', 'settingsGroups'));
    }

    /**
     * Update system settings
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        try {
            // Handle logo upload
            if ($request->hasFile('university_logo')) {
                $request->validate([
                    'university_logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);

                // Delete old logo if exists
                $oldLogo = \App\Models\Setting::get('university_logo');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }

                // Store new logo
                $logoPath = $request->file('university_logo')->store('logos', 'public');
                \App\Services\SettingsService::set('university_logo', $logoPath, 'string', 'University logo file path');
            }

            // Get all settings from database to determine types
            $allSettings = \App\Models\Setting::all()->keyBy('key');

            // Update all submitted settings
            foreach ($request->except(['_token', '_method', 'university_logo']) as $key => $value) {
                if ($allSettings->has($key)) {
                    $setting = $allSettings->get($key);
                    $type = $setting->type;
                    $description = $setting->description;

                    // Convert boolean values
                    if ($type === 'boolean') {
                        $value = $value ? '1' : '0';
                    }

                    \App\Services\SettingsService::set($key, $value, $type, $description);
                }
            }

            // Clear cache after update
            \App\Services\SettingsService::clearCache();

            return redirect()->back()->with('success', __('app.settings_updated_successfully'));
        } catch (\Exception $e) {
            \Log::error('Settings update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', __('app.settings_update_failed') . ': ' . $e->getMessage())
                ->withInput();
        }
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
        'users_file' => 'required|file|mimes:xlsx,csv,xls',
        'default_role' => 'nullable|string|in:student,teacher,department_head',
        'speciality_id' => 'required|exists:specialities,id',
    ]);

    // try {
        $file = $request->file('users_file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, true);

        // Use selected speciality from form

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        // Process data rows starting from row 5 as specified
        foreach ($rows as $index => $row) {
            // dd($index);
          if ($index == 1) {
    $speciality = null;

    if (!empty($row['A'])) {
        // On découpe avec ":" et on récupère la partie après
        $parts = explode(':', $row['A'], 2);
        if (isset($parts[1])) {
            $speciality = trim($parts[1]);
        } else {
            // Si ":" n'existe pas, on prend tout le texte
            $speciality = trim($row['A']);
        }
    }

}


            if ($index < 7) continue; // Skip until row 5

            try {
                // Initialize variables
                $matricule = null;
                $nom = null;
                $prenom = null;
                $section = null;
                $groupe = null;

                // Debug: Log the row data for debugging
                \Log::info("Processing row {$index}: " . json_encode($row));

                // Try original C, D, E mapping first (as per specification)
                if (isset($row['C']) && isset($row['D']) && isset($row['E']) &&
                    !empty(trim($row['C'])) && !empty(trim($row['D'])) && !empty(trim($row['E']))) {
                    $matricule = trim($row['D']);  // Column C: Matricule
                    $nom = trim($row['E']);        // Column D: Nom (Last Name)
                    $prenom = trim($row['F']);     // Column E: Prénom (First Name)
                    $section = trim($row['G'] ?? '');  // Column F: Section
                    $groupe = trim($row['H'] ?? '');   // Column G: Groupe

                    \Log::info("Using direct column mapping - Matricule: {$matricule}, Nom: {$nom}, Prénom: {$prenom}");
                } else {
                    // Fallback: Filter out empty cells and get all values in order
                    $allValues = [];
                    foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'] as $col) {
                        if (isset($row[$col]) && !empty(trim($row[$col]))) {
                            $allValues[] = trim($row[$col]);
                        }
                    }

                    \Log::info("Fallback mode - All values: " . json_encode($allValues));

                    // Skip if no data in row
                    if (count($allValues) < 3) {
                        $skipped++;
                        continue;
                    }

                    // For fallback, assume order is: matricule, nom, prenom, section, groupe
                    // Don't try to identify matricule - just use positional mapping
                    if (count($allValues) >= 3) {
                        $matricule = $allValues[0];  // First value is matricule
                        $nom = $allValues[1];        // Second value is nom
                        $prenom = $allValues[2];     // Third value is prenom
                        $section = isset($allValues[3]) ? $allValues[3] : '';
                        $groupe = isset($allValues[4]) ? $allValues[4] : '';

                        \Log::info("Fallback extraction - Matricule: {$matricule}, Nom: {$nom}, Prénom: {$prenom}");
                    }
                }

                // Validate required fields
                if (!$matricule || !$nom || !$prenom) {
                    $skipped++;
                    continue; // Skip invalid rows
                }

                // Clean and format data
                $matricule = strtoupper(trim($matricule));
                $nom = ucfirst(strtolower(trim($nom)));
                $prenom = ucfirst(strtolower(trim($prenom)));
                $fullName = $nom . ' ' . $prenom;  // Nom + Prénom (Last Name + First Name)

                // Debug: Log final processed values
                \Log::info("Final values - Matricule: '{$matricule}', Nom: '{$nom}', Prénom: '{$prenom}', FullName: '{$fullName}'");

                // Generate email and password based on matricule
                $email = strtolower($matricule . '@gmail.com');
                $password = $matricule; // Password is the matricule

                // Check if user exists
                $existingUser = User::where('matricule', $matricule)->first();

                // Update or create student
                $userData = [
                    'name'          => $fullName,
                    'email'         => $email,
                    'role'          => $request->input('default_role', 'student'),
                    'department'    => 'Computer Science', // Fixed department
                    'speciality_id' => $request->speciality_id,
                    'password'      => bcrypt($password),
                    'matricule'     => $matricule,
                    // Additional fields for better data storage
                    'first_name'    => $prenom,
                    'last_name'     => $nom,
                ];

                if ($existingUser) {
                    $existingUser->update($userData);
                    $updated++;
                } else {
                    User::create(array_merge(
                        ['matricule' => $matricule],
                        $userData
                    ));
                    $created++;
                }

            } catch (\Exception $e) {
                $errors[] = "Row {$index}: {$e->getMessage()}";
                $skipped++;
            }
        }

        $message = "Bulk import completed! Created: {$created}, Updated: {$updated}, Skipped: {$skipped}";
        if (!empty($errors)) {
            $message .= ". Errors: " . count($errors);
        }

        return redirect()->back()
            ->with('success', $message)
            ->with('import_details', [
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
                'errors' => $errors
            ]);

    // } catch (\Exception $e) {
    //     return redirect()->back()
    //         ->with('error', 'Bulk import failed: ' . $e->getMessage())
    //         ->withInput();
    // }
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
     * Display teams management page
     */
    public function teams(Request $request): View
    {
        $query = Team::with(['members.user', 'subject', 'supervisor']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('members.user', function($userQ) use ($search) {
                      $userQ->where('name', 'like', "%{$search}%")
                            ->orWhere('matricule', 'like', "%{$search}%");
                  })
                  ->orWhereHas('subject', function($subjectQ) use ($search) {
                      $subjectQ->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply academic year filter
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $teams = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $statuses = ['forming', 'active', 'completed'];
        $academicYears = Team::distinct()->pluck('academic_year')->filter();

        return view('admin.teams.index', compact('teams', 'statuses', 'academicYears'));
    }

    /**
     * Assign subject to team
     */
    public function assignSubjectToTeam(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $team = Team::findOrFail($validated['team_id']);
        $subject = Subject::findOrFail($validated['subject_id']);

        // Check if subject is available
        if ($subject->status !== 'validated') {
            return redirect()->back()
                ->with('error', __('app.subject_not_available_for_assignment'));
        }

        try {
            // Use transaction with locking to prevent race conditions
            \DB::transaction(function () use ($team, $subject) {
                // Lock the teams table to check if subject is already assigned
                $assignedTeam = Team::where('subject_id', $subject->id)
                    ->where('id', '!=', $team->id)
                    ->lockForUpdate()
                    ->first();

                if ($assignedTeam) {
                    throw new \Exception(__('app.subject_already_assigned'));
                }

                // Assign subject to team
                $team->update([
                    'subject_id' => $subject->id,
                    'supervisor_id' => $subject->teacher_id,
                    'status' => 'active'
                ]);

                // Create project if it doesn't exist
                if (!$team->project) {
                    \App\Models\Project::create([
                        'title' => $subject->title,
                        'description' => $subject->description,
                        'subject_id' => $subject->id,
                        'team_id' => $team->id,
                        'supervisor_id' => $subject->teacher_id,
                        'status' => 'active',
                        'academic_year' => $team->academic_year,
                    ]);
                }
            });

            return redirect()->back()
                ->with('success', __('app.subject_assigned_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove subject assignment from team
     */
    public function removeSubjectFromTeam(Team $team): RedirectResponse
    {
        $team->update([
            'subject_id' => null,
            'supervisor_id' => null,
            'status' => 'forming'
        ]);

        // Delete associated project if it exists
        if ($team->project) {
            $team->project->delete();
        }

        return redirect()->back()
            ->with('success', __('app.subject_assignment_removed'));
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
     * Show pending subjects for approval
     */
    public function pendingSubjects(): View
    {
        $pendingSubjects = Subject::with(['teacher'])
            ->where('status', 'pending_validation')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.subjects.pending', compact('pendingSubjects'));
    }

    /**
     * Show all subjects
     */
    public function allSubjects(): View
    {
        $subjects = Subject::with(['teacher'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => Subject::count(),
            'pending' => Subject::where('status', 'pending_validation')->count(),
            'validated' => Subject::where('status', 'validated')->count(),
            'rejected' => Subject::where('status', 'rejected')->count(),
        ];

        return view('admin.subjects.all', compact('subjects', 'stats'));
    }

    /**
     * Approve a subject
     */
    public function approveSubject(Request $request, Subject $subject): RedirectResponse
    {
        if ($subject->status !== 'pending_validation') {
            return redirect()->back()->with('error', 'Subject is not pending validation.');
        }

        $subject->update([
            'status' => 'validated',
            'validated_at' => now(),
            'validated_by' => Auth::id(),
            'validation_feedback' => $request->input('feedback', 'Subject approved by admin.')
        ]);

        return redirect()->back()->with('success', "Subject '{$subject->title}' has been approved.");
    }

    /**
     * Reject a subject
     */
    public function rejectSubject(Request $request, Subject $subject): RedirectResponse
    {
        if ($subject->status !== 'pending_validation') {
            return redirect()->back()->with('error', 'Subject is not pending validation.');
        }

        $validated = $request->validate([
            'feedback' => 'required|string|max:500'
        ]);

        $subject->update([
            'status' => 'rejected',
            'validated_at' => now(),
            'validated_by' => Auth::id(),
            'validation_feedback' => $validated['feedback']
        ]);

        return redirect()->back()->with('success', "Subject '{$subject->title}' has been rejected.");
    }

    /**
     * Maintenance page
     */
    public function maintenance(): View
    {
        return view('admin.maintenance');
    }

    // =====================================================================
    // STUDENT MARKS MANAGEMENT
    // =====================================================================

    /**
     * Show marks management
     */
    public function marks(): View
    {
        $marks = StudentMark::with(['student', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.marks.index', compact('marks'));
    }

    /**
     * Show create mark form
     */
    public function createMark(): View
    {
        $students = User::where('role', 'student')->get();
        return view('admin.marks.create', compact('students'));
    }

    /**
     * Store new mark
     */
    public function storeMark(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_name' => 'required|string|max:255',
            'academic_year' => 'required|string',
            'semester' => 'nullable|string|max:10',
            'max_mark' => 'required|numeric|min:1|max:100',
            'notes' => 'nullable|string|max:1000',
            // Required marks
            'mark_1' => 'required|numeric|min:0',
            'mark_2' => 'required|numeric|min:0',
            // Optional marks
            'mark_3' => 'nullable|numeric|min:0',
            'mark_4' => 'nullable|numeric|min:0',
            'mark_5' => 'nullable|numeric|min:0',
        ]);

        // Add dynamic validation for marks against max_mark
        $request->validate([
            'mark_1' => 'max:' . $request->max_mark,
            'mark_2' => 'max:' . $request->max_mark,
            'mark_3' => 'nullable|max:' . $request->max_mark,
            'mark_4' => 'nullable|max:' . $request->max_mark,
            'mark_5' => 'nullable|max:' . $request->max_mark,
        ]);

        try {
            // Check if student already has marks for this subject and academic year
            $existingMark = StudentMark::where('user_id', $request->user_id)
                ->where('subject_name', $request->subject_name)
                ->where('academic_year', $request->academic_year)
                ->when($request->semester, function ($query) use ($request) {
                    return $query->where('semester', $request->semester);
                })
                ->first();

            if ($existingMark) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', __('app.student_already_has_marks_for_this_subject'));
            }

            // Calculate simple average of entered marks
            $marks = array_filter([
                $request->mark_1,
                $request->mark_2,
                $request->mark_3,
                $request->mark_4,
                $request->mark_5
            ], function($mark) {
                return $mark !== null && $mark !== '';
            });

            $averageMark = count($marks) > 0 ? array_sum($marks) / count($marks) : 0;

            StudentMark::create([
                'user_id' => $request->user_id,
                'subject_name' => $request->subject_name,
                'mark' => $averageMark,
                'max_mark' => $request->max_mark,
                'semester' => $request->semester,
                'academic_year' => $request->academic_year,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                // Individual marks
                'mark_1' => $request->mark_1,
                'mark_2' => $request->mark_2,
                'mark_3' => $request->mark_3,
                'mark_4' => $request->mark_4,
                'mark_5' => $request->mark_5,
                // Max marks (all same as max_mark)
                'max_mark_1' => $request->max_mark,
                'max_mark_2' => $request->max_mark,
                'max_mark_3' => $request->max_mark,
                'max_mark_4' => $request->max_mark,
                'max_mark_5' => $request->max_mark,
                // Equal weights
                'weight_1' => 20,
                'weight_2' => 20,
                'weight_3' => 20,
                'weight_4' => 20,
                'weight_5' => 20,
            ]);

            return redirect()->route('admin.marks')
                ->with('success', __('app.mark_added_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('app.failed_to_add_mark') . ': ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show edit mark form
     */
    public function editMark(StudentMark $mark): View
    {
        $students = User::where('role', 'student')->get();
        return view('admin.marks.edit', compact('mark', 'students'));
    }

    /**
     * Update mark
     */
    public function updateMark(Request $request, StudentMark $mark): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_name' => 'required|string|max:255',
            'academic_year' => 'required|string',
            'semester' => 'nullable|string|max:10',
            'max_mark' => 'required|numeric|min:1|max:100',
            'notes' => 'nullable|string|max:1000',
            // Required marks
            'mark_1' => 'required|numeric|min:0',
            'mark_2' => 'required|numeric|min:0',
            // Optional marks
            'mark_3' => 'nullable|numeric|min:0',
            'mark_4' => 'nullable|numeric|min:0',
            'mark_5' => 'nullable|numeric|min:0',
        ]);

        // Add dynamic validation for marks against max_mark
        $request->validate([
            'mark_1' => 'max:' . $request->max_mark,
            'mark_2' => 'max:' . $request->max_mark,
            'mark_3' => 'nullable|max:' . $request->max_mark,
            'mark_4' => 'nullable|max:' . $request->max_mark,
            'mark_5' => 'nullable|max:' . $request->max_mark,
        ]);

        try {
            // Check if another mark exists for this student, subject, and academic year (excluding current mark)
            $existingMark = StudentMark::where('user_id', $request->user_id)
                ->where('subject_name', $request->subject_name)
                ->where('academic_year', $request->academic_year)
                ->when($request->semester, function ($query) use ($request) {
                    return $query->where('semester', $request->semester);
                })
                ->where('id', '!=', $mark->id)
                ->first();

            if ($existingMark) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', __('app.student_already_has_marks_for_this_subject'));
            }

            // Calculate simple average of entered marks
            $marks = array_filter([
                $request->mark_1,
                $request->mark_2,
                $request->mark_3,
                $request->mark_4,
                $request->mark_5
            ], function($markValue) {
                return $markValue !== null && $markValue !== '';
            });

            $averageMark = count($marks) > 0 ? array_sum($marks) / count($marks) : 0;

            $mark->update([
                'user_id' => $request->user_id,
                'subject_name' => $request->subject_name,
                'mark' => $averageMark,
                'max_mark' => $request->max_mark,
                'semester' => $request->semester,
                'academic_year' => $request->academic_year,
                'notes' => $request->notes,
                // Individual marks
                'mark_1' => $request->mark_1,
                'mark_2' => $request->mark_2,
                'mark_3' => $request->mark_3,
                'mark_4' => $request->mark_4,
                'mark_5' => $request->mark_5,
                // Max marks (all same as max_mark)
                'max_mark_1' => $request->max_mark,
                'max_mark_2' => $request->max_mark,
                'max_mark_3' => $request->max_mark,
                'max_mark_4' => $request->max_mark,
                'max_mark_5' => $request->max_mark,
                // Keep equal weights
                'weight_1' => 20,
                'weight_2' => 20,
                'weight_3' => 20,
                'weight_4' => 20,
                'weight_5' => 20,
            ]);

            return redirect()->route('admin.marks')
                ->with('success', __('app.mark_updated_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('app.failed_to_update_mark') . ': ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete a student mark
     */
    public function destroyMark(StudentMark $mark): RedirectResponse
    {
        try {
            $studentName = $mark->student->name;
            $mark->delete();

            return redirect()->route('admin.marks')
                ->with('success', __('app.mark_deleted_successfully', ['student' => $studentName]));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('app.failed_to_delete_mark') . ': ' . $e->getMessage());
        }
    }

    /**
     * Show bulk marks creation form.
     */
    public function bulkMarksCreate(): View
    {
        $students = User::where('role', 'student')
            ->orderBy('name')
            ->get();

        return view('admin.marks.bulk-create', compact('students'));
    }

    /**
     * Store bulk marks for a student.
     */
    public function bulkMarksStore(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            // Required marks
            'mark_1' => 'required|numeric|min:0|max:20',
            'mark_2' => 'required|numeric|min:0|max:20',
            // Optional marks
            'mark_3' => 'nullable|numeric|min:0|max:20',
            'mark_4' => 'nullable|numeric|min:0|max:20',
            'mark_5' => 'nullable|numeric|min:0|max:20',
        ]);

        try {
            $user = User::findOrFail($request->user_id);

            // Calculate simple average of entered marks
            $marks = array_filter([
                $request->mark_1,
                $request->mark_2,
                $request->mark_3,
                $request->mark_4,
                $request->mark_5
            ], function($mark) {
                return $mark !== null && $mark !== '';
            });
            $averageMark = count($marks) > 0 ? array_sum($marks) / count($marks) : 0;

            $academicYear = date('Y') . '-' . (date('Y') + 1);

            // Check if student already has marks for current academic year
            $existingMark = StudentMark::where('user_id', $user->id)
                ->where('subject_name', 'General Assessment')
                ->where('academic_year', $academicYear)
                ->first();

            $markData = [
                'user_id' => $user->id,
                'subject_name' => 'General Assessment', // Default subject name
                'mark' => $averageMark, // Average of all marks
                'max_mark' => 20, // Standard max mark
                'semester' => null, // Not used anymore
                'academic_year' => $academicYear, // Current academic year
                'notes' => null, // Not used anymore
                'created_by' => Auth::id(),
                // Individual marks
                'mark_1' => $request->mark_1,
                'mark_2' => $request->mark_2,
                'mark_3' => $request->mark_3,
                'mark_4' => $request->mark_4,
                'mark_5' => $request->mark_5,
                // Max marks (all 20)
                'max_mark_1' => 20,
                'max_mark_2' => 20,
                'max_mark_3' => 20,
                'max_mark_4' => 20,
                'max_mark_5' => 20,
                // Equal weights
                'weight_1' => 20,
                'weight_2' => 20,
                'weight_3' => 20,
                'weight_4' => 20,
                'weight_5' => 20,
            ];

            if ($existingMark) {
                // Update existing mark
                $existingMark->update($markData);
                $message = __('app.marks_updated_successfully', ['student' => $user->name]);
            } else {
                // Create new mark
                StudentMark::create($markData);
                $message = __('app.marks_saved_successfully', ['count' => 1, 'student' => $user->name]);
            }

            return redirect()->route('admin.marks')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('app.failed_to_save_marks') . ': ' . $e->getMessage());
        }
    }

    /**
     * Show student marks by semester.
     */
    public function studentMarksBySemester(User $user, string $semester): View
    {
        $marks = StudentMark::where('user_id', $user->id)
            ->where('semester', $semester)
            ->orderBy('subject_name')
            ->get();

        return view('admin.marks.semester-view', compact('user', 'semester', 'marks'));
    }

    /**
     * Get student marks summary.
     */
    public function studentMarksSummary(User $user): View
    {
        $marksBySemester = StudentMark::where('user_id', $user->id)
            ->selectRaw('semester, COUNT(*) as subject_count, AVG(mark/max_mark*100) as average_percentage')
            ->groupBy('semester')
            ->orderBy('semester')
            ->get();

        $allMarks = StudentMark::where('user_id', $user->id)
            ->orderBy('semester')
            ->orderBy('subject_name')
            ->get();

        return view('admin.marks.student-summary', compact('user', 'marksBySemester', 'allMarks'));
    }

    /**
     * Show bulk marks entry form for all students.
     */
    public function bulkAllStudentsCreate(): View
    {
        // Get all students
        $students = User::where('role', 'student')
            ->orderBy('name')
            ->get();

        return view('admin.marks.bulk-all-students', compact('students'));
    }

    /**
     * Store bulk marks for all students.
     */
    public function bulkAllStudentsStore(Request $request): RedirectResponse
    {
        $request->validate([
            'selected_students' => 'required|array|min:1',
            'selected_students.*' => 'exists:users,id',
            'mark_1' => 'required|array',
            'mark_1.*' => 'required|numeric|min:0|max:20',
            'mark_2' => 'required|array',
            'mark_2.*' => 'required|numeric|min:0|max:20',
            'mark_3' => 'array',
            'mark_3.*' => 'nullable|numeric|min:0|max:20',
            'mark_4' => 'array',
            'mark_4.*' => 'nullable|numeric|min:0|max:20',
            'mark_5' => 'array',
            'mark_5.*' => 'nullable|numeric|min:0|max:20',
        ]);

        try {
            $processedStudents = 0;

            foreach ($request->selected_students as $studentId) {
                // Only process if mark_1 and mark_2 are provided (required)
                if (!empty($request->mark_1[$studentId]) && !empty($request->mark_2[$studentId])) {

                    // Check if student already has marks for current academic year
                    $academicYear = date('Y') . '-' . (date('Y') + 1);
                    $existingMark = StudentMark::where('user_id', $studentId)
                        ->where('subject_name', 'General Assessment')
                        ->where('academic_year', $academicYear)
                        ->first();

                    // Calculate average of all provided marks
                    $marks = array_filter([
                        $request->mark_1[$studentId],
                        $request->mark_2[$studentId],
                        $request->mark_3[$studentId] ?? null,
                        $request->mark_4[$studentId] ?? null,
                        $request->mark_5[$studentId] ?? null
                    ], function($mark) {
                        return $mark !== null && $mark !== '';
                    });
                    $averageMark = count($marks) > 0 ? array_sum($marks) / count($marks) : 0;

                    $markData = [
                        'user_id' => $studentId,
                        'subject_name' => 'General Assessment', // Default since no subject selection
                        'semester' => null,
                        'academic_year' => $academicYear,
                        'mark' => $averageMark, // Required average mark field
                        'max_mark' => 20, // Standard max mark
                        'mark_1' => $request->mark_1[$studentId],
                        'mark_2' => $request->mark_2[$studentId],
                        'mark_3' => $request->mark_3[$studentId] ?? null,
                        'mark_4' => $request->mark_4[$studentId] ?? null,
                        'mark_5' => $request->mark_5[$studentId] ?? null,
                        // Set max marks (all 20)
                        'max_mark_1' => 20,
                        'max_mark_2' => 20,
                        'max_mark_3' => 20,
                        'max_mark_4' => 20,
                        'max_mark_5' => 20,
                        // Set equal weights (20% each)
                        'weight_1' => 20,
                        'weight_2' => 20,
                        'weight_3' => 20,
                        'weight_4' => 20,
                        'weight_5' => 20,
                        'created_by' => Auth::id(),
                    ];

                    if ($existingMark) {
                        // Update existing record
                        $existingMark->update($markData);
                    } else {
                        // Create new record
                        StudentMark::create($markData);
                    }

                    $processedStudents++;
                }
            }

            $message = __('app.bulk_marks_saved_successfully_simple', [
                'count' => $processedStudents,
            ]);

            return redirect()->route('admin.marks')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('app.failed_to_save_marks') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display student alerts management page
     */
    public function alerts(): View
    {
        $alerts = StudentAlert::with(['student', 'respondedBy'])
            ->orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.alerts.index', compact('alerts'));
    }

    /**
     * Show alert details and response form
     */
    public function showAlert($id): View
    {
        $alert = StudentAlert::with(['student', 'respondedBy'])->findOrFail($id);
        return view('admin.alerts.show', compact('alert'));
    }

    /**
     * Respond to student alert
     */
    public function respondToAlert(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'admin_response' => 'required|string|max:1000',
        ]);

        $alert = StudentAlert::findOrFail($id);
        $alert->markAsResponded($request->admin_response, Auth::id());

        return redirect()->route('admin.alerts')
            ->with('success', __('app.response_sent_successfully'));
    }

    /**
     * Show pending subject requests
     */
    public function subjectRequests(): View
    {
        $subjectRequests = SubjectRequest::with(['team.members.user', 'subject.teacher', 'requestedBy'])
            ->orderBy('requested_at', 'asc')
            ->paginate(20);

        return view('admin.subject-requests.index', compact('subjectRequests'));
    }

    /**
     * Approve a subject request
     */
    public function approveSubjectRequest(Request $request, SubjectRequest $subjectRequest): RedirectResponse
    {
        $request->validate([
            'admin_response' => 'nullable|string|max:1000'
        ]);

        if (!$subjectRequest->isPending()) {
            return redirect()->back()
                ->with('error', __('app.request_already_processed'));
        }

        $subjectRequest->approve(Auth::user(), $request->admin_response);

        return redirect()->back()
            ->with('success', __('app.subject_request_approved'));
    }

    /**
     * Reject a subject request
     */
    public function rejectSubjectRequest(Request $request, SubjectRequest $subjectRequest): RedirectResponse
    {
        $request->validate([
            'admin_response' => 'required|string|max:1000'
        ]);

        if (!$subjectRequest->isPending()) {
            return redirect()->back()
                ->with('error', __('app.request_already_processed'));
        }

        $subjectRequest->reject(Auth::user(), $request->admin_response);

        return redirect()->back()
            ->with('success', __('app.subject_request_rejected'));
    }

    /**
     * Show birth certificates pending approval
     */
    public function birthCertificates(Request $request): View
    {
        $query = User::where('role', 'student')
            ->whereNotNull('birth_certificate_path');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('birth_certificate_status', $request->status);
        }

        // Search by name or email
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.birth-certificates.index', compact('students'));
    }

    /**
     * Approve a birth certificate
     */
    public function approveBirthCertificate(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        $user->update([
            'birth_certificate_status' => 'approved',
            'birth_certificate_approved_at' => now(),
            'birth_certificate_approved_by' => Auth::id(),
            'birth_certificate_notes' => $request->notes,
        ]);

        return redirect()->back()
            ->with('success', __('app.birth_certificate_approved', ['name' => $user->name]));
    }

    /**
     * Reject a birth certificate
     */
    public function rejectBirthCertificate(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'notes' => 'required|string|max:1000'
        ]);

        $user->update([
            'birth_certificate_status' => 'rejected',
            'birth_certificate_approved_at' => now(),
            'birth_certificate_approved_by' => Auth::id(),
            'birth_certificate_notes' => $request->notes,
        ]);

        return redirect()->back()
            ->with('success', __('app.birth_certificate_rejected', ['name' => $user->name]));
    }

    /**
     * Get available subjects for assignment (AJAX endpoint)
     */
    public function getAvailableSubjects(): JsonResponse
    {
        $availableSubjects = Subject::where('status', 'validated')
            ->whereNotIn('id', Team::whereNotNull('subject_id')->pluck('subject_id'))
            ->with('teacher')
            ->get()
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'title' => $subject->title,
                    'description' => Str::limit($subject->description, 150),
                    'teacher_name' => $subject->teacher->name ?? __('app.no_teacher'),
                    'level' => $subject->level ?? '',
                ];
            });

        return response()->json([
            'subjects' => $availableSubjects,
            'count' => $availableSubjects->count()
        ]);
    }

    /**
     * Debug subject preferences issues
     */
    public function debugPreferences()
    {
        $teams = Team::with(['members.user', 'subjectPreferences.subject'])->get();
        $deadline = AllocationDeadline::active()->first();

        $debugInfo = [
            'current_time' => now()->format('Y-m-d H:i:s'),
            'timezone' => config('app.timezone'),
            'teams_count' => $teams->count(),
            'deadline' => null,
            'teams' => []
        ];

        if ($deadline) {
            $debugInfo['deadline'] = [
                'id' => $deadline->id,
                'title' => $deadline->title,
                'status' => $deadline->status,
                'preferences_start' => $deadline->preferences_start->format('Y-m-d H:i:s'),
                'preferences_deadline' => $deadline->preferences_deadline->format('Y-m-d H:i:s'),
                'is_active' => $deadline->isActive(),
                'can_students_choose' => $deadline->canStudentsChoose(),
                'current_time_between_dates' => now()->between($deadline->preferences_start, $deadline->preferences_deadline)
            ];
        }

        foreach ($teams as $team) {
            $hasAllocatedSubject = $team->subjectPreferences()->whereNotNull('allocated_at')->exists();
            $allocatedSubject = $team->subjectPreferences()->whereNotNull('allocated_at')->first();

            $debugInfo['teams'][] = [
                'id' => $team->id,
                'name' => $team->name,
                'status' => $team->status,
                'member_count' => $team->members->count(),
                'is_complete' => $team->isComplete(),
                'has_allocated_subject' => $hasAllocatedSubject,
                'allocated_subject' => $allocatedSubject ? [
                    'subject_title' => $allocatedSubject->subject->title,
                    'allocated_at' => $allocatedSubject->allocated_at->format('Y-m-d H:i:s')
                ] : null,
                'can_manage_preferences' => $team->canManagePreferences(),
                'preferences_count' => $team->subjectPreferences->count()
            ];
        }

        return response()->json($debugInfo, 200, [], JSON_PRETTY_PRINT);
    }
}
