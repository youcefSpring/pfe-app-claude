<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Subject;
use App\Models\TeamSubjectPreference;
use App\Services\ProjectAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ConflictController extends Controller
{
    public function __construct(private ProjectAssignmentService $assignmentService)
    {
        $this->middleware('auth');
        $this->middleware('role:chef_master|admin_pfe');
    }

    /**
     * Display conflicts dashboard
     */
    public function index(): View
    {
        $conflicts = $this->getSubjectConflicts();
        $stats = $this->getConflictStats($conflicts);

        return view('pfe.conflicts.index', [
            'conflicts' => $conflicts,
            'stats' => $stats
        ]);
    }

    /**
     * Show detailed conflict resolution page
     */
    public function show(Subject $subject): View
    {
        $this->authorize('resolveConflicts', $subject);

        $competingTeams = $this->getCompetingTeams($subject);

        return view('pfe.conflicts.show', [
            'subject' => $subject,
            'competingTeams' => $competingTeams
        ]);
    }

    /**
     * Resolve conflict by assigning subject to selected team
     */
    public function resolve(Request $request, Subject $subject): RedirectResponse
    {
        $this->authorize('resolveConflicts', $subject);

        $request->validate([
            'selected_team_id' => 'required|exists:teams,id',
            'resolution_notes' => 'nullable|string|max:1000'
        ]);

        $selectedTeam = Team::findOrFail($request->selected_team_id);
        $competingTeams = $this->getCompetingTeams($subject);

        // Validate that selected team is actually competing for this subject
        if (!$competingTeams->contains('id', $selectedTeam->id)) {
            return back()->withErrors(['selected_team_id' => 'Selected team is not competing for this subject']);
        }

        DB::transaction(function () use ($subject, $selectedTeam, $competingTeams, $request) {
            // Create project assignment
            $this->assignmentService->resolveConflict([
                'subject_id' => $subject->id,
                'competing_teams' => $competingTeams->pluck('id')->toArray()
            ], $selectedTeam);

            // Log resolution decision
            $this->logConflictResolution($subject, $selectedTeam, $request->resolution_notes);

            // Remove preferences for non-selected teams
            TeamSubjectPreference::where('subject_id', $subject->id)
                ->whereIn('team_id', $competingTeams->where('id', '!=', $selectedTeam->id)->pluck('id'))
                ->delete();

            // Update subject status to assigned
            $subject->update(['status' => 'assigned']);
        });

        return redirect()->route('pfe.conflicts.index')
            ->with('success', "Subject '{$subject->title}' assigned to team '{$selectedTeam->name}'");
    }

    /**
     * Bulk resolve multiple conflicts
     */
    public function bulkResolve(Request $request): RedirectResponse
    {
        $request->validate([
            'resolutions' => 'required|array',
            'resolutions.*.subject_id' => 'required|exists:subjects,id',
            'resolutions.*.team_id' => 'required|exists:teams,id',
        ]);

        $resolvedCount = 0;

        DB::transaction(function () use ($request, &$resolvedCount) {
            foreach ($request->resolutions as $resolution) {
                $subject = Subject::find($resolution['subject_id']);
                $team = Team::find($resolution['team_id']);

                if ($subject && $team) {
                    $competingTeams = $this->getCompetingTeams($subject);

                    if ($competingTeams->contains('id', $team->id)) {
                        $this->assignmentService->resolveConflict([
                            'subject_id' => $subject->id,
                            'competing_teams' => $competingTeams->pluck('id')->toArray()
                        ], $team);

                        $subject->update(['status' => 'assigned']);
                        $resolvedCount++;
                    }
                }
            }
        });

        return back()->with('success', "Resolved {$resolvedCount} conflicts successfully");
    }

    /**
     * Auto-resolve conflicts using predefined criteria
     */
    public function autoResolve(Request $request): RedirectResponse
    {
        $request->validate([
            'criteria' => 'required|in:merit,registration_order,team_size,random',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id'
        ]);

        $conflicts = $this->getSubjectConflicts();

        if ($request->subject_ids) {
            $conflicts = $conflicts->whereIn('id', $request->subject_ids);
        }

        $resolvedCount = 0;

        DB::transaction(function () use ($conflicts, $request, &$resolvedCount) {
            foreach ($conflicts as $subject) {
                $competingTeams = $this->getCompetingTeams($subject);
                $selectedTeam = $this->selectTeamByCriteria($competingTeams, $request->criteria);

                if ($selectedTeam) {
                    $this->assignmentService->resolveConflict([
                        'subject_id' => $subject->id,
                        'competing_teams' => $competingTeams->pluck('id')->toArray()
                    ], $selectedTeam);

                    $subject->update(['status' => 'assigned']);
                    $resolvedCount++;
                }
            }
        });

        return back()->with('success', "Auto-resolved {$resolvedCount} conflicts using {$request->criteria} criteria");
    }

    /**
     * Get subjects with conflicts (multiple teams wanting same subject)
     */
    private function getSubjectConflicts()
    {
        return Subject::where('status', 'published')
            ->whereHas('teamPreferences', function ($query) {
                $query->select('subject_id')
                    ->groupBy('subject_id')
                    ->havingRaw('COUNT(DISTINCT team_id) > max_teams');
            })
            ->with(['supervisor', 'teamPreferences.team'])
            ->get();
    }

    /**
     * Get teams competing for a specific subject
     */
    private function getCompetingTeams(Subject $subject)
    {
        return Team::whereHas('subjectPreferences', function ($query) use ($subject) {
            $query->where('subject_id', $subject->id);
        })
        ->with(['leader', 'members.user', 'subjectPreferences' => function ($query) use ($subject) {
            $query->where('subject_id', $subject->id);
        }])
        ->get()
        ->map(function ($team) {
            $team->team_score = $this->calculateTeamScore($team);
            $team->average_grade = $this->calculateAverageGrade($team);
            return $team;
        });
    }

    /**
     * Calculate team conflict resolution score
     */
    private function calculateTeamScore(Team $team): float
    {
        $score = 0;

        // Formation date (earlier = higher priority)
        if ($team->created_at) {
            $daysAgo = now()->diffInDays($team->created_at);
            $score += max(0, 30 - $daysAgo);
        }

        // Team size optimization
        $score += ($team->members_count == 3) ? 20 : 10;

        // Preference order
        $preferenceOrder = $team->subjectPreferences->first()?->preference_order ?? 5;
        $score += (6 - $preferenceOrder) * 10;

        return $score;
    }

    /**
     * Calculate team average grade (mock implementation)
     */
    private function calculateAverageGrade(Team $team): float
    {
        // This would integrate with academic records system
        // For now, return a mock average
        return rand(12, 20) / 1.0;
    }

    /**
     * Select team based on resolution criteria
     */
    private function selectTeamByCriteria($teams, string $criteria)
    {
        switch ($criteria) {
            case 'merit':
                return $teams->sortByDesc('average_grade')->first();

            case 'registration_order':
                return $teams->sortBy('created_at')->first();

            case 'team_size':
                return $teams->sortByDesc('members_count')->first();

            case 'random':
                return $teams->random();

            default:
                return $teams->sortByDesc('team_score')->first();
        }
    }

    /**
     * Get conflict statistics
     */
    private function getConflictStats($conflicts): array
    {
        $totalConflicts = $conflicts->count();
        $totalAffectedTeams = 0;
        $conflictsBySeverity = ['high' => 0, 'medium' => 0, 'low' => 0];

        foreach ($conflicts as $subject) {
            $competingTeamsCount = $subject->teamPreferences->count();
            $totalAffectedTeams += $competingTeamsCount;

            if ($competingTeamsCount > 5) {
                $conflictsBySeverity['high']++;
            } elseif ($competingTeamsCount > 3) {
                $conflictsBySeverity['medium']++;
            } else {
                $conflictsBySeverity['low']++;
            }
        }

        return [
            'total_conflicts' => $totalConflicts,
            'total_affected_teams' => $totalAffectedTeams,
            'by_severity' => $conflictsBySeverity,
            'resolution_rate' => $totalConflicts > 0 ? 0 : 100 // To be calculated based on resolved conflicts
        ];
    }

    /**
     * Log conflict resolution for audit trail
     */
    private function logConflictResolution(Subject $subject, Team $selectedTeam, ?string $notes): void
    {
        // This would typically go to an audit log table
        // For now, we can use Laravel's built-in logging
        logger('Conflict Resolution', [
            'subject_id' => $subject->id,
            'subject_title' => $subject->title,
            'selected_team_id' => $selectedTeam->id,
            'selected_team_name' => $selectedTeam->name,
            'resolved_by' => auth()->id(),
            'resolution_notes' => $notes,
            'resolved_at' => now()
        ]);
    }
}