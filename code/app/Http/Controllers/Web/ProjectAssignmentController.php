<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\PFE\AssignProjectsRequest;
use App\Models\Team;
use App\Models\Subject;
use App\Models\User;
use App\Services\ProjectAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ProjectAssignmentController extends Controller
{
    public function __construct(private ProjectAssignmentService $assignmentService)
    {
        $this->middleware('auth');
        $this->middleware('role:chef_master|admin_pfe');
    }

    /**
     * Display project assignment dashboard
     */
    public function index(): View
    {
        $stats = $this->getAssignmentStats();
        $unassignedTeams = Team::where('status', 'validated')
            ->whereDoesntHave('project')
            ->with(['leader', 'members.user'])
            ->get();

        $availableSubjects = Subject::where('status', 'published')
            ->whereDoesntHave('projects')
            ->with('supervisor')
            ->get();

        return view('pfe.assignments.index', [
            'stats' => $stats,
            'unassigned_teams' => $unassignedTeams,
            'available_subjects' => $availableSubjects
        ]);
    }

    /**
     * Show automatic assignment configuration
     */
    public function showAutoAssignment(): View
    {
        $teams = Team::where('status', 'validated')
            ->with(['subjectPreferences.subject', 'leader'])
            ->get();

        $subjects = Subject::where('status', 'published')
            ->with('supervisor')
            ->get();

        return view('pfe.assignments.auto-assign', [
            'teams' => $teams,
            'subjects' => $subjects,
            'assignment_preview' => $this->getAssignmentPreview()
        ]);
    }

    /**
     * Execute automatic project assignment
     */
    public function executeAutoAssignment(AssignProjectsRequest $request): RedirectResponse
    {
        $results = $this->assignmentService->assignProjects();

        session()->flash('assignment_results', $results);

        return redirect()->route('pfe.assignments.results')
            ->with('success', "Assignment completed. {$results['success_rate']}% success rate.");
    }

    /**
     * Show assignment results
     */
    public function showResults(): View
    {
        $results = session('assignment_results', [
            'assignments' => [],
            'conflicts' => [],
            'success_rate' => 0
        ]);

        return view('pfe.assignments.results', [
            'results' => $results
        ]);
    }

    /**
     * Manual assignment interface
     */
    public function showManualAssignment(): View
    {
        $unassignedTeams = Team::where('status', 'validated')
            ->whereDoesntHave('project')
            ->with(['leader', 'members.user', 'subjectPreferences.subject'])
            ->get();

        $availableSubjects = Subject::where('status', 'published')
            ->with(['supervisor', 'teamPreferences.team'])
            ->get();

        return view('pfe.assignments.manual', [
            'unassigned_teams' => $unassignedTeams,
            'available_subjects' => $availableSubjects
        ]);
    }

    /**
     * Execute manual assignment
     */
    public function executeManualAssignment(Request $request): RedirectResponse
    {
        $request->validate([
            'assignments' => 'required|array',
            'assignments.*.team_id' => 'required|exists:teams,id',
            'assignments.*.subject_id' => 'required|exists:subjects,id'
        ]);

        $assignedCount = 0;

        DB::transaction(function () use ($request, &$assignedCount) {
            foreach ($request->assignments as $assignment) {
                $team = Team::find($assignment['team_id']);
                $subject = Subject::find($assignment['subject_id']);

                if ($team && $subject && $team->status === 'validated' && $subject->status === 'published') {
                    // Check if already assigned
                    if (!$team->project && !$subject->projects()->exists()) {
                        $this->assignmentService->resolveConflict([
                            'subject_id' => $subject->id,
                            'competing_teams' => [$team->id]
                        ], $team);

                        $assignedCount++;
                    }
                }
            }
        });

        return back()->with('success', "Successfully assigned {$assignedCount} projects manually");
    }

    /**
     * External projects management
     */
    public function showExternalProjects(): View
    {
        $externalProjects = Subject::where('external_company', '!=', null)
            ->with(['supervisor', 'projects.team'])
            ->get();

        $pendingExternalRequests = Team::whereHas('externalProjectRequests', function ($query) {
            $query->where('status', 'pending');
        })
        ->with(['externalProjectRequests', 'leader'])
        ->get();

        $availableSupervisors = User::role('teacher')
            ->withCount('supervisedPfeProjects')
            ->orderBy('supervised_pfe_projects_count')
            ->get();

        return view('pfe.assignments.external', [
            'external_projects' => $externalProjects,
            'pending_requests' => $pendingExternalRequests,
            'available_supervisors' => $availableSupervisors
        ]);
    }

    /**
     * Approve external project request
     */
    public function approveExternalProject(Request $request): RedirectResponse
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'project_data' => 'required|array',
            'supervisor_id' => 'required|exists:users,id'
        ]);

        $team = Team::findOrFail($request->team_id);
        $projectData = array_merge($request->project_data, [
            'supervisor_id' => $request->supervisor_id
        ]);

        try {
            $project = $this->assignmentService->assignExternalProject($team, $projectData);

            return back()->with('success',
                "External project '{$project->subject->title}' approved and assigned to team '{$team->name}'"
            );
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Balance supervisor workload
     */
    public function balanceSupervisorWorkload(): RedirectResponse
    {
        $rebalancedCount = $this->rebalanceSupervisorAssignments();

        return back()->with('success',
            "Rebalanced {$rebalancedCount} project assignments across supervisors"
        );
    }

    /**
     * Get assignment statistics
     */
    private function getAssignmentStats(): array
    {
        $totalTeams = Team::where('status', 'validated')->count();
        $assignedTeams = Team::whereHas('project')->count();
        $totalSubjects = Subject::where('status', 'published')->count();
        $assignedSubjects = Subject::whereHas('projects')->count();

        return [
            'total_teams' => $totalTeams,
            'assigned_teams' => $assignedTeams,
            'unassigned_teams' => $totalTeams - $assignedTeams,
            'assignment_rate' => $totalTeams > 0 ? ($assignedTeams / $totalTeams) * 100 : 0,
            'total_subjects' => $totalSubjects,
            'assigned_subjects' => $assignedSubjects,
            'available_subjects' => $totalSubjects - $assignedSubjects,
            'conflicts' => $this->getConflictCount()
        ];
    }

    /**
     * Get assignment preview for auto-assignment
     */
    private function getAssignmentPreview(): array
    {
        // This would run a dry-run of the assignment algorithm
        $teams = Team::where('status', 'validated')->with('subjectPreferences.subject')->get();
        $subjects = Subject::where('status', 'published')->get();

        $potentialAssignments = [];
        $potentialConflicts = [];

        // Simplified preview logic
        foreach ($teams as $team) {
            $firstPreference = $team->subjectPreferences()->orderBy('preference_order')->first();
            if ($firstPreference) {
                $subject = $firstPreference->subject;
                if (!isset($potentialAssignments[$subject->id])) {
                    $potentialAssignments[$subject->id] = [
                        'subject' => $subject,
                        'teams' => []
                    ];
                }
                $potentialAssignments[$subject->id]['teams'][] = $team;

                if (count($potentialAssignments[$subject->id]['teams']) > 1) {
                    $potentialConflicts[] = $potentialAssignments[$subject->id];
                }
            }
        }

        return [
            'potential_assignments' => array_filter($potentialAssignments, fn($a) => count($a['teams']) === 1),
            'potential_conflicts' => $potentialConflicts,
            'estimated_success_rate' => count($potentialAssignments) > 0 ?
                (count(array_filter($potentialAssignments, fn($a) => count($a['teams']) === 1)) / count($potentialAssignments)) * 100 : 0
        ];
    }

    /**
     * Get current conflict count
     */
    private function getConflictCount(): int
    {
        return Subject::where('status', 'published')
            ->whereHas('teamPreferences', function ($query) {
                $query->select('subject_id')
                    ->groupBy('subject_id')
                    ->havingRaw('COUNT(DISTINCT team_id) > 1');
            })
            ->count();
    }

    /**
     * Rebalance supervisor assignments
     */
    private function rebalanceSupervisorAssignments(): int
    {
        $supervisors = User::role('teacher')
            ->withCount('supervisedPfeProjects')
            ->get();

        $maxWorkload = $supervisors->max('supervised_pfe_projects_count');
        $minWorkload = $supervisors->min('supervised_pfe_projects_count');

        $rebalancedCount = 0;

        // Simple rebalancing: move projects from overloaded to underloaded supervisors
        if ($maxWorkload - $minWorkload > 2) {
            $overloadedSupervisors = $supervisors->where('supervised_pfe_projects_count', $maxWorkload);
            $underloadedSupervisors = $supervisors->where('supervised_pfe_projects_count', $minWorkload);

            foreach ($overloadedSupervisors as $overloaded) {
                $projectToMove = $overloaded->supervisedPfeProjects()->first();
                $underloaded = $underloadedSupervisors->first();

                if ($projectToMove && $underloaded) {
                    $projectToMove->update(['supervisor_id' => $underloaded->id]);
                    $rebalancedCount++;
                }
            }
        }

        return $rebalancedCount;
    }
}