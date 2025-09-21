<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PFE\CreateUserRequest;
use App\Models\User;
use App\Models\Room;
use App\Services\ReportingService;
use App\Services\ProjectAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct(
        private ReportingService $reportingService,
        private ProjectAssignmentService $assignmentService
    ) {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin_pfe|chef_master');
    }

    /**
     * Get all users with filters
     */
    public function users(Request $request): JsonResponse
    {
        $query = User::with('roles');

        // Apply filters
        if ($request->has('role')) {
            $query->role($request->role);
        }

        if ($request->has('department')) {
            $query->where('department', $request->department);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('student_id', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $users = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'last_page' => $users->lastPage()
            ]
        ]);
    }

    /**
     * Create a new user
     */
    public function createUser(CreateUserRequest $request): JsonResponse
    {
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'student_id' => $request->student_id,
            'department' => $request->department,
            'is_active' => $request->boolean('is_active', true)
        ]);

        // Assign roles
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            // Assign default role based on student_id
            $defaultRole = $request->student_id ? 'student' : 'teacher';
            $user->assignRole($defaultRole);
        }

        return response()->json([
            'user' => $user->load('roles'),
            'message' => 'User created successfully'
        ], 201);
    }

    /**
     * Update user roles
     */
    public function updateUserRoles(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'roles' => 'required|array|min:1',
            'roles.*' => 'string|exists:roles,name'
        ]);

        $user->syncRoles($request->roles);

        return response()->json([
            'user' => $user->load('roles'),
            'message' => 'User roles updated successfully'
        ]);
    }

    /**
     * Toggle user active status
     */
    public function toggleUserStatus(User $user): JsonResponse
    {
        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'user' => $user,
            'message' => $user->is_active ? 'User activated' : 'User deactivated'
        ]);
    }

    /**
     * Get all rooms
     */
    public function rooms(): JsonResponse
    {
        $rooms = Room::orderBy('name')->get();

        return response()->json([
            'rooms' => $rooms
        ]);
    }

    /**
     * Create a new room
     */
    public function createRoom(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:rooms',
            'capacity' => 'required|integer|min:1|max:100',
            'equipment' => 'nullable|array',
            'equipment.*' => 'string|max:100',
            'location' => 'nullable|string|max:100',
            'is_available' => 'boolean'
        ]);

        $room = Room::create($request->all());

        return response()->json([
            'room' => $room,
            'message' => 'Room created successfully'
        ], 201);
    }

    /**
     * Update a room
     */
    public function updateRoom(Request $request, Room $room): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:50|unique:rooms,name,' . $room->id,
            'capacity' => 'sometimes|required|integer|min:1|max:100',
            'equipment' => 'nullable|array',
            'equipment.*' => 'string|max:100',
            'location' => 'nullable|string|max:100',
            'is_available' => 'boolean'
        ]);

        $room->update($request->all());

        return response()->json([
            'room' => $room->fresh(),
            'message' => 'Room updated successfully'
        ]);
    }

    /**
     * Get system statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $year = $request->get('year', now()->year);
        $department = $request->get('department');

        $stats = $this->reportingService->generateDashboardStats($request->user());

        // Add additional admin-specific stats
        $additionalStats = [
            'year' => $year,
            'department' => $department,
            'system_health' => [
                'pending_validations' => \App\Models\Subject::where('status', 'submitted')->count(),
                'teams_without_projects' => \App\Models\Team::where('status', 'validated')
                    ->whereDoesntHave('project')->count(),
                'overdue_projects' => \App\Models\PfeProject::where('expected_end_date', '<', now())
                    ->whereNotIn('status', ['completed', 'defended'])->count(),
                'upcoming_defenses' => \App\Models\Defense::where('defense_date', '>=', now())
                    ->where('defense_date', '<=', now()->addWeek())
                    ->where('status', 'scheduled')->count()
            ]
        ];

        return response()->json(array_merge($stats, $additionalStats));
    }

    /**
     * Resolve assignment conflicts
     */
    public function resolveConflict(Request $request): JsonResponse
    {
        $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
            'winning_team_id' => 'required|integer|exists:teams,id',
            'losing_teams' => 'required|array|min:1',
            'losing_teams.*' => 'integer|exists:teams,id',
            'criteria' => 'required|string|max:500'
        ]);

        $subject = \App\Models\Subject::findOrFail($request->subject_id);
        $winningTeam = \App\Models\Team::findOrFail($request->winning_team_id);

        $project = $this->assignmentService->resolveConflict([
            'subject_id' => $request->subject_id,
            'competing_teams' => array_merge([$request->winning_team_id], $request->losing_teams)
        ], $winningTeam);

        // Log the resolution
        activity()
            ->performedOn($subject)
            ->causedBy($request->user())
            ->withProperties([
                'winning_team_id' => $request->winning_team_id,
                'losing_teams' => $request->losing_teams,
                'criteria' => $request->criteria
            ])
            ->log('conflict_resolved');

        return response()->json([
            'resolution' => [
                'project' => $project->load(['subject:id,title', 'team:id,name']),
                'criteria' => $request->criteria,
                'resolved_by' => $request->user()->first_name . ' ' . $request->user()->last_name,
                'resolved_at' => now()
            ],
            'message' => 'Conflict resolved successfully'
        ]);
    }

    /**
     * Bulk assign projects
     */
    public function bulkAssignProjects(Request $request): JsonResponse
    {
        $request->validate([
            'assignments' => 'required|array|min:1',
            'assignments.*.subject_id' => 'required|integer|exists:subjects,id',
            'assignments.*.team_id' => 'required|integer|exists:teams,id'
        ]);

        $results = [];
        $errors = [];

        foreach ($request->assignments as $assignment) {
            try {
                $project = \App\Models\PfeProject::create([
                    'subject_id' => $assignment['subject_id'],
                    'team_id' => $assignment['team_id'],
                    'supervisor_id' => \App\Models\Subject::find($assignment['subject_id'])->supervisor_id,
                    'status' => 'assigned',
                    'start_date' => now()->addDays(7),
                    'expected_end_date' => now()->addMonths(6)
                ]);

                $results[] = $project->load(['subject:id,title', 'team:id,name']);
            } catch (\Exception $e) {
                $errors[] = [
                    'subject_id' => $assignment['subject_id'],
                    'team_id' => $assignment['team_id'],
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'successful_assignments' => $results,
            'failed_assignments' => $errors,
            'success_count' => count($results),
            'error_count' => count($errors),
            'message' => count($results) . ' projects assigned successfully'
        ]);
    }

    /**
     * Get system activity log
     */
    public function activityLog(Request $request): JsonResponse
    {
        $query = \Spatie\Activitylog\Models\Activity::with(['causer', 'subject'])
            ->latest();

        if ($request->has('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        if ($request->has('causer_id')) {
            $query->where('causer_id', $request->causer_id);
        }

        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $activities = $query->paginate($request->get('per_page', 50));

        return response()->json([
            'data' => $activities->items(),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'total' => $activities->total(),
                'per_page' => $activities->perPage(),
                'last_page' => $activities->lastPage()
            ]
        ]);
    }
}