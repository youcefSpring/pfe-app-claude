<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AllocationDeadline;
use App\Models\Subject;
use App\Models\Team;
use App\Models\SubjectAllocation;
use App\Models\Project;
use App\Services\AutoAllocationService;
use App\Services\SubjectAllocationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AllocationController extends Controller
{
    public function __construct(
        private AutoAllocationService $autoAllocationService,
        private SubjectAllocationService $subjectAllocationService
    ) {}

    /**
     * Display allocation management dashboard
     */
    public function index(Request $request): View
    {
        $deadlines = AllocationDeadline::with('creator')
            ->when($request->academic_year, function($query, $year) {
                return $query->where('academic_year', $year);
            })
            ->when($request->level, function($query, $level) {
                return $query->where('level', $level);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $academicYears = AllocationDeadline::distinct()->pluck('academic_year');
        $levels = AllocationDeadline::distinct()->pluck('level');

        return view('admin.allocations.index', compact('deadlines', 'academicYears', 'levels'));
    }

    /**
     * Show allocation details for a specific deadline
     */
    public function show(AllocationDeadline $deadline): View
    {
        $deadline->load(['allocations.student', 'allocations.subject']);

        $stats = [
            'total_teams' => Team::where('academic_year', $deadline->academic_year)
                ->where('level', $deadline->level)
                ->count(),
            'teams_with_preferences' => Team::where('academic_year', $deadline->academic_year)
                ->where('level', $deadline->level)
                ->whereHas('preferences', function($q) use ($deadline) {
                    $q->where('allocation_deadline_id', $deadline->id);
                })
                ->count(),
            'allocated_teams' => $deadline->allocations()->where('status', 'confirmed')->count(),
            'available_subjects' => Subject::where('level', $deadline->level)
                ->where('academic_year', $deadline->academic_year)
                ->where('is_validated', true)
                ->whereDoesntHave('allocations', function($q) use ($deadline) {
                    $q->where('allocation_deadline_id', $deadline->id)
                      ->where('status', 'confirmed');
                })
                ->count()
        ];

        $unallocatedTeams = Team::where('academic_year', $deadline->academic_year)
            ->where('level', $deadline->level)
            ->whereDoesntHave('projects', function($q) use ($deadline) {
                $q->where('academic_year', $deadline->academic_year);
            })
            ->with(['members.user', 'preferences.subject'])
            ->get();

        $availableSubjects = Subject::where('level', $deadline->level)
            ->where('academic_year', $deadline->academic_year)
            ->where('is_validated', true)
            ->whereDoesntHave('allocations', function($q) use ($deadline) {
                $q->where('allocation_deadline_id', $deadline->id)
                  ->where('status', 'confirmed');
            })
            ->with('teacher')
            ->get();

        return view('admin.allocations.show', compact(
            'deadline',
            'stats',
            'unallocatedTeams',
            'availableSubjects'
        ));
    }

    /**
     * Perform auto-allocation for a deadline
     */
    public function performAutoAllocation(AllocationDeadline $deadline): RedirectResponse
    {
        try {
            $result = $this->autoAllocationService->performAutoAllocation($deadline);

            $message = sprintf(
                'Auto-allocation completed! %d teams allocated, %d conflicts resolved, %d teams need second round.',
                $result['statistics']['total_allocated'],
                $result['statistics']['conflict_resolutions'],
                $result['statistics']['teams_without_subjects']
            );

            return redirect()->route('admin.allocations.show', $deadline)
                ->with('success', $message)
                ->with('allocation_result', $result);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Auto-allocation failed: ' . $e->getMessage());
        }
    }

    /**
     * Manually assign a subject to a team
     */
    public function manualAssignment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'deadline_id' => 'required|exists:allocation_deadlines,id',
            'team_id' => 'required|exists:teams,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $deadline = AllocationDeadline::find($validated['deadline_id']);
        $team = Team::find($validated['team_id']);
        $subject = Subject::find($validated['subject_id']);

        // Check if subject is available
        $existingAllocation = SubjectAllocation::where('subject_id', $subject->id)
            ->where('allocation_deadline_id', $deadline->id)
            ->where('status', 'confirmed')
            ->first();

        if ($existingAllocation) {
            return redirect()->back()
                ->with('error', 'Subject is already allocated to another team.');
        }

        // Check if team already has an allocation
        $teamAllocation = SubjectAllocation::where('allocation_deadline_id', $deadline->id)
            ->whereHas('student', function($q) use ($team) {
                $q->whereIn('id', $team->members->pluck('user_id'));
            })
            ->where('status', 'confirmed')
            ->first();

        if ($teamAllocation) {
            return redirect()->back()
                ->with('error', 'Team already has a subject allocated.');
        }

        DB::beginTransaction();
        try {
            // Get team leader
            $teamLeader = $team->members()->where('is_leader', true)->first()
                ?? $team->members()->first();

            if (!$teamLeader) {
                throw new \Exception("Team {$team->name} has no members");
            }

            // Create allocation
            SubjectAllocation::create([
                'allocation_deadline_id' => $deadline->id,
                'student_id' => $teamLeader->user_id,
                'subject_id' => $subject->id,
                'student_preference_order' => 99, // Manual assignment
                'student_average' => 0,
                'allocation_rank' => 1,
                'allocation_method' => 'manual_admin',
                'status' => 'confirmed',
                'confirmed_by' => Auth::id(),
                'confirmed_at' => now(),
            ]);

            // Create project
            Project::create([
                'team_id' => $team->id,
                'subject_id' => $subject->id,
                'supervisor_id' => $subject->teacher_id,
                'title' => $subject->title,
                'description' => $subject->description,
                'type' => $subject->is_external ? 'external' : 'internal',
                'status' => 'assigned',
                'academic_year' => $deadline->academic_year,
                'level' => $deadline->level,
                'start_date' => now(),
                'end_date' => now()->addMonths(6),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', __('app.subject_manually_assigned', ['subject' => $subject->title, 'team' => $team->name]));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Manual assignment failed: ' . $e->getMessage());
        }
    }

    /**
     * Initialize second round for teams without subjects
     */
    public function initializeSecondRound(Request $request, AllocationDeadline $deadline): RedirectResponse
    {
        $validated = $request->validate([
            'second_round_start' => 'required|date|after:now',
            'second_round_deadline' => 'required|date|after:second_round_start',
        ]);

        try {
            $deadline->initializeSecondRound(
                \Carbon\Carbon::parse($validated['second_round_start']),
                \Carbon\Carbon::parse($validated['second_round_deadline'])
            );

            return redirect()->back()
                ->with('success', __('app.second_round_initialized'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to initialize second round: ' . $e->getMessage());
        }
    }

    /**
     * Remove allocation (for manual corrections)
     */
    public function removeAllocation(SubjectAllocation $allocation): RedirectResponse
    {
        if ($allocation->status !== 'confirmed') {
            return redirect()->back()
                ->with('error', 'Only confirmed allocations can be removed.');
        }

        DB::beginTransaction();
        try {
            // Remove related project if exists
            $project = Project::where('subject_id', $allocation->subject_id)
                ->where('academic_year', $allocation->deadline->academic_year)
                ->first();

            if ($project) {
                $project->delete();
            }

            // Remove allocation
            $allocation->delete();

            DB::commit();

            return redirect()->back()
                ->with('success', __('app.allocation_removed_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to remove allocation: ' . $e->getMessage());
        }
    }

    /**
     * Run preference-based subject allocation
     */
    public function runPreferenceAllocation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year' => 'required|string'
        ]);

        try {
            $results = $this->subjectAllocationService->allocateSubjects($validated['academic_year']);
            $summary = $this->subjectAllocationService->getAllocationSummary($results);
            $report = $this->subjectAllocationService->generateAllocationReport($results);

            // Store the report in session for display
            session()->flash('allocation_report', $report);

            return redirect()->back()
                ->with('success', __('app.allocation_completed_summary', ['allocated' => $summary['allocated_teams'], 'resolved' => $summary['conflicts_resolved']]))
                ->with('allocation_summary', $summary);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Preference allocation failed: ' . $e->getMessage());
        }
    }

    /**
     * Show preference allocation preview
     */
    public function previewPreferenceAllocation(Request $request): View
    {
        $validated = $request->validate([
            'academic_year' => 'required|string'
        ]);

        $academicYear = $validated['academic_year'];

        // Get teams with preferences
        $teamsWithPreferences = Team::where('academic_year', $academicYear)
            ->whereHas('subjectPreferences')
            ->with([
                'subjectPreferences.subject',
                'members.user'
            ])
            ->get();

        // Get available subjects
        $availableSubjects = Subject::where('status', 'validated')
            ->where('academic_year', $academicYear)
            ->whereDoesntHave('projects')
            ->get();

        return view('admin.allocations.preview-preference', compact(
            'teamsWithPreferences',
            'availableSubjects',
            'academicYear'
        ));
    }
}
