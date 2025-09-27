<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PFE\CreateDefenseRequest;
use App\Http\Requests\PFE\UpdateDefenseRequest;
use App\Models\Defense;
use App\Services\DefenseSchedulingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DefenseController extends Controller
{
    public function __construct(private DefenseSchedulingService $schedulingService)
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of defenses
     */
    public function index(Request $request): JsonResponse
    {
        $query = Defense::with([
            'project.subject:id,title',
            'project.team:id,name',
            'room:id,name,location',
            'juryPresident:id,first_name,last_name',
            'juryExaminer:id,first_name,last_name',
            'jurySupervisor:id,first_name,last_name'
        ]);

        // Apply filters
        if ($request->has('date_from')) {
            $query->where('defense_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('defense_date', '<=', $request->date_to);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        // Role-based filtering
        $user = $request->user();
        if ($user->hasRole('student')) {
            $query->whereHas('project.team.members', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->hasRole('teacher')) {
            $query->where(function ($q) use ($user) {
                $q->where('jury_president_id', $user->id)
                  ->orWhere('jury_examiner_id', $user->id)
                  ->orWhere('jury_supervisor_id', $user->id);
            });
        }

        $defenses = $query->orderBy('defense_date')
                          ->orderBy('start_time')
                          ->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $defenses->items(),
            'meta' => [
                'current_page' => $defenses->currentPage(),
                'total' => $defenses->total(),
                'per_page' => $defenses->perPage(),
                'last_page' => $defenses->lastPage()
            ]
        ]);
    }

    /**
     * Store a newly created defense
     */
    public function store(CreateDefenseRequest $request): JsonResponse
    {
        // Generate jury composition if not provided
        $data = $request->validated();
        if (!isset($data['jury_supervisor_id'])) {
            $project = \App\Models\PfeProject::find($data['project_id']);
            $data['jury_supervisor_id'] = $project->supervisor_id;
        }

        $defense = Defense::create($data);

        return response()->json([
            'defense' => $defense->load([
                'project.subject:id,title',
                'project.team:id,name',
                'room:id,name',
                'juryPresident:id,first_name,last_name',
                'juryExaminer:id,first_name,last_name',
                'jurySupervisor:id,first_name,last_name'
            ]),
            'message' => 'Defense scheduled successfully'
        ], 201);
    }

    /**
     * Display the specified defense
     */
    public function show(Defense $defense): JsonResponse
    {
        $this->authorize('view', $defense);

        $defense->load([
            'project.subject:id,title,description',
            'project.team.members.user:id,first_name,last_name,student_id',
            'project.supervisor:id,first_name,last_name',
            'room:id,name,location,capacity,equipment',
            'juryPresident:id,first_name,last_name,email',
            'juryExaminer:id,first_name,last_name,email',
            'jurySupervisor:id,first_name,last_name,email'
        ]);

        return response()->json([
            'defense' => $defense,
            'project' => $defense->project,
            'team' => $defense->project->team,
            'jury' => [
                'president' => $defense->juryPresident,
                'examiner' => $defense->juryExaminer,
                'supervisor' => $defense->jurySupervisor
            ],
            'room' => $defense->room
        ]);
    }

    /**
     * Update the specified defense
     */
    public function update(UpdateDefenseRequest $request, Defense $defense): JsonResponse
    {
        // Handle rescheduling
        if ($request->has(['defense_date', 'start_time', 'room_id'])) {
            $newSchedule = $request->only(['defense_date', 'start_time', 'end_time', 'room_id']);
            $defense = $this->schedulingService->rescheduleDefense($defense, $newSchedule);
            $message = 'Defense rescheduled successfully';
        } else {
            $defense->update($request->validated());
            $message = 'Defense updated successfully';
        }

        return response()->json([
            'defense' => $defense->load([
                'project.subject:id,title',
                'project.team:id,name',
                'room:id,name'
            ]),
            'message' => $message
        ]);
    }

    /**
     * Submit grades for defense
     */
    public function submitGrades(Request $request, Defense $defense): JsonResponse
    {
        $this->authorize('grade', $defense);

        $request->validate([
            'final_grade' => 'nullable|numeric|min:0|max:20',
            'grade_president' => 'nullable|numeric|min:0|max:20',
            'grade_examiner' => 'nullable|numeric|min:0|max:20',
            'grade_supervisor' => 'nullable|numeric|min:0|max:20',
            'observations' => 'nullable|string|max:2000'
        ]);

        $user = $request->user();
        $updates = [];

        // Check which grades the user can assign
        if ($user->id === $defense->jury_president_id || $user->hasRole(['admin_pfe', 'chef_master'])) {
            if ($request->has('grade_president')) {
                $updates['grade_president'] = $request->grade_president;
            }
        }

        if ($user->id === $defense->jury_examiner_id || $user->hasRole(['admin_pfe', 'chef_master'])) {
            if ($request->has('grade_examiner')) {
                $updates['grade_examiner'] = $request->grade_examiner;
            }
        }

        if ($user->id === $defense->jury_supervisor_id || $user->hasRole(['admin_pfe', 'chef_master'])) {
            if ($request->has('grade_supervisor')) {
                $updates['grade_supervisor'] = $request->grade_supervisor;
            }
        }

        // Calculate final grade if all individual grades are present
        if ($request->has('final_grade')) {
            $updates['final_grade'] = $request->final_grade;
        } elseif (isset($updates['grade_president'], $updates['grade_examiner'], $updates['grade_supervisor'])) {
            $updates['final_grade'] = ($updates['grade_president'] + $updates['grade_examiner'] + $updates['grade_supervisor']) / 3;
        }

        if ($request->has('observations')) {
            $updates['observations'] = $request->observations;
        }

        // Update defense status to completed if final grade is provided
        if (isset($updates['final_grade'])) {
            $updates['status'] = 'completed';
            $updates['completed_at'] = now();
        }

        $defense->update($updates);

        return response()->json([
            'defense' => $defense->fresh(),
            'message' => 'Grades submitted successfully'
        ]);
    }

    /**
     * Generate PV document for defense
     */
    public function generatePv(Defense $defense): JsonResponse
    {
        $this->authorize('generatePv', $defense);

        if ($defense->status !== 'completed') {
            return response()->json([
                'error' => 'Invalid Status',
                'message' => 'Defense must be completed to generate PV'
            ], 422);
        }

        // Generate PV document (implementation would use PDF generation service)
        $pvData = [
            'defense' => $defense,
            'project' => $defense->project,
            'team' => $defense->project->team,
            'jury' => [
                'president' => $defense->juryPresident,
                'examiner' => $defense->juryExaminer,
                'supervisor' => $defense->jurySupervisor
            ]
        ];

        $pvPath = app(FileManagementService::class)->uploadPvDocument($pvData, $defense->id);

        $defense->update([
            'pv_generated' => true,
            'pv_file_path' => $pvPath
        ]);

        return response()->json([
            'pv_file_url' => url("api/v1/files/{$pvPath}"),
            'message' => 'PV generated successfully'
        ]);
    }

    /**
     * Auto-schedule multiple defenses
     */
    public function autoSchedule(Request $request): JsonResponse
    {
        $this->authorize('schedule', Defense::class);

        $request->validate([
            'project_ids' => 'required|array|min:1',
            'project_ids.*' => 'integer|exists:projects,id',
            'date_range.start' => 'required|date|after:today',
            'date_range.end' => 'required|date|after:date_range.start',
            'time_slots' => 'array',
            'time_slots.*.start' => 'required|date_format:H:i',
            'time_slots.*.end' => 'required|date_format:H:i|after:time_slots.*.start'
        ]);

        $result = $this->schedulingService->scheduleDefenses(
            $request->project_ids,
            [
                'start_date' => $request->input('date_range.start'),
                'end_date' => $request->input('date_range.end'),
                'time_slots' => $request->input('time_slots', [])
            ]
        );

        return response()->json([
            'scheduled_defenses' => $result['scheduled'],
            'conflicts' => $result['conflicts'],
            'success_rate' => $result['success_rate'],
            'message' => 'Auto-scheduling completed'
        ]);
    }

    /**
     * Check scheduling availability
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room_id' => 'required|integer|exists:rooms,id',
            'jury_ids' => 'required|array|min:3|max:3',
            'jury_ids.*' => 'integer|exists:users,id'
        ]);

        $conflicts = $this->schedulingService->checkAvailability(
            $request->date,
            $request->start_time,
            $request->end_time,
            $request->jury_ids,
            $request->room_id
        );

        return response()->json([
            'available' => empty($conflicts),
            'conflicts' => $conflicts
        ]);
    }
}