<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Team;
use App\Models\TeamSubjectPreference;
use App\Models\TeamMember;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class StudentSubjectController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    /**
     * Browse available subjects
     */
    public function index(Request $request): View
    {
        $student = auth()->user();
        $team = $this->getCurrentTeam($student);

        // Check if student has a validated team
        if (!$team || $team->status !== 'validated') {
            return view('pfe.student.subjects.no-team', [
                'team' => $team,
                'message' => $team ? 'Your team needs to be validated before selecting subjects.' : 'You need to join a team before selecting subjects.'
            ]);
        }

        $query = Subject::where('status', 'published')
            ->with(['supervisor', 'teamPreferences' => function($q) use ($team) {
                $q->where('team_id', $team->id);
            }])
            ->withCount('teamPreferences');

        // Apply filters
        if ($request->has('department')) {
            $query->whereHas('supervisor', function($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        if ($request->has('keywords')) {
            $keywords = explode(',', $request->keywords);
            $query->where(function($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhereJsonContains('keywords', trim($keyword));
                }
            });
        }

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'popularity');
        switch ($sortBy) {
            case 'popularity':
                $query->orderBy('team_preferences_count', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'title':
                $query->orderBy('title');
                break;
        }

        $subjects = $query->paginate(12);

        $currentPreferences = $team->subjectPreferences()
            ->with('subject.supervisor')
            ->orderBy('preference_order')
            ->get();

        $departments = $this->getAvailableDepartments();
        $popularKeywords = $this->getPopularKeywords();
        $selectionStats = $this->getSelectionStats($team);

        return view('pfe.student.subjects.index', [
            'subjects' => $subjects,
            'team' => $team,
            'current_preferences' => $currentPreferences,
            'departments' => $departments,
            'popular_keywords' => $popularKeywords,
            'selection_stats' => $selectionStats,
            'filters' => $request->only(['department', 'keywords', 'search', 'sort'])
        ]);
    }

    /**
     * Show subject details
     */
    public function show(Subject $subject): View
    {
        $student = auth()->user();
        $team = $this->getCurrentTeam($student);

        $subject->load([
            'supervisor',
            'teamPreferences.team.leader',
            'projects.team'
        ]);

        $hasPreference = false;
        $preferenceOrder = null;

        if ($team) {
            $preference = $team->subjectPreferences()
                ->where('subject_id', $subject->id)
                ->first();

            if ($preference) {
                $hasPreference = true;
                $preferenceOrder = $preference->preference_order;
            }
        }

        $competingTeams = $this->getCompetingTeams($subject);
        $competitionLevel = $this->calculateCompetitionLevel($subject);
        $similarSubjects = $this->findSimilarSubjects($subject);

        return view('pfe.student.subjects.show', [
            'subject' => $subject,
            'team' => $team,
            'has_preference' => $hasPreference,
            'preference_order' => $preferenceOrder,
            'competing_teams' => $competingTeams,
            'competition_level' => $competitionLevel,
            'similar_subjects' => $similarSubjects,
            'can_select' => $this->canSelectSubject($team, $subject)
        ]);
    }

    /**
     * Add subject to team preferences
     */
    public function addPreference(Request $request, Subject $subject): RedirectResponse
    {
        $student = auth()->user();
        $team = $this->getCurrentTeam($student);

        if (!$this->canSelectSubject($team, $subject)) {
            return back()->withErrors(['error' => 'Cannot select this subject at this time.']);
        }

        $request->validate([
            'preference_order' => 'required|integer|min:1|max:5',
            'justification' => 'nullable|string|max:1000'
        ]);

        // Check if preference order is already taken
        $existingPreference = $team->subjectPreferences()
            ->where('preference_order', $request->preference_order)
            ->first();

        if ($existingPreference) {
            return back()->withErrors(['preference_order' => 'This preference order is already taken.']);
        }

        // Check if subject is already in preferences
        if ($team->subjectPreferences()->where('subject_id', $subject->id)->exists()) {
            return back()->withErrors(['error' => 'Subject is already in your preferences.']);
        }

        TeamSubjectPreference::create([
            'team_id' => $team->id,
            'subject_id' => $subject->id,
            'preference_order' => $request->preference_order,
            'justification' => $request->justification,
            'selected_by' => $student->id,
            'selected_at' => now()
        ]);

        // Notify team members
        $this->notifyTeamMembers($team, 'preference_added', [
            'subject_title' => $subject->title,
            'preference_order' => $request->preference_order,
            'selected_by' => $student->first_name . ' ' . $student->last_name
        ]);

        return back()->with('success', 'Subject added to your preferences successfully!');
    }

    /**
     * Remove subject from preferences
     */
    public function removePreference(Subject $subject): RedirectResponse
    {
        $student = auth()->user();
        $team = $this->getCurrentTeam($student);

        if (!$team) {
            return back()->withErrors(['error' => 'You need to be in a team to manage preferences.']);
        }

        $preference = $team->subjectPreferences()
            ->where('subject_id', $subject->id)
            ->first();

        if (!$preference) {
            return back()->withErrors(['error' => 'Subject is not in your preferences.']);
        }

        // Check if team leader or selection deadline has not passed
        if (!$this->canModifyPreferences($team)) {
            return back()->withErrors(['error' => 'Cannot modify preferences at this time.']);
        }

        $preferenceOrder = $preference->preference_order;
        $preference->delete();

        // Reorder remaining preferences
        $team->subjectPreferences()
            ->where('preference_order', '>', $preferenceOrder)
            ->decrement('preference_order');

        // Notify team members
        $this->notifyTeamMembers($team, 'preference_removed', [
            'subject_title' => $subject->title,
            'removed_by' => $student->first_name . ' ' . $student->last_name
        ]);

        return back()->with('success', 'Subject removed from preferences successfully!');
    }

    /**
     * Reorder preferences
     */
    public function reorderPreferences(Request $request): RedirectResponse
    {
        $student = auth()->user();
        $team = $this->getCurrentTeam($student);

        if (!$this->canModifyPreferences($team)) {
            return back()->withErrors(['error' => 'Cannot modify preferences at this time.']);
        }

        $request->validate([
            'preferences' => 'required|array|max:5',
            'preferences.*.id' => 'required|exists:team_subject_preferences,id',
            'preferences.*.order' => 'required|integer|min:1|max:5'
        ]);

        DB::transaction(function () use ($request, $team) {
            foreach ($request->preferences as $pref) {
                TeamSubjectPreference::where('id', $pref['id'])
                    ->where('team_id', $team->id)
                    ->update(['preference_order' => $pref['order']]);
            }
        });

        // Notify team members
        $this->notifyTeamMembers($team, 'preferences_reordered', [
            'reordered_by' => $student->first_name . ' ' . $student->last_name
        ]);

        return back()->with('success', 'Preferences reordered successfully!');
    }

    /**
     * Submit final preferences
     */
    public function submitPreferences(Request $request): RedirectResponse
    {
        $student = auth()->user();
        $team = $this->getCurrentTeam($student);

        if (!$team || $team->leader_id !== $student->id) {
            return back()->withErrors(['error' => 'Only team leaders can submit final preferences.']);
        }

        if ($team->subjectPreferences()->count() < 3) {
            return back()->withErrors(['error' => 'You must select at least 3 preferences before submitting.']);
        }

        $request->validate([
            'confirmation' => 'required|accepted',
            'additional_notes' => 'nullable|string|max:1000'
        ]);

        $team->update([
            'preferences_submitted_at' => now(),
            'preferences_submitted_by' => $student->id,
            'status' => 'preferences_submitted',
            'additional_notes' => $request->additional_notes
        ]);

        // Notify team members
        $this->notifyTeamMembers($team, 'preferences_submitted', [
            'submitted_by' => $student->first_name . ' ' . $student->last_name
        ]);

        // Notify chef master
        $chefMaster = \App\Models\User::role('chef_master')
            ->where('department', $team->leader->department)
            ->first();

        if ($chefMaster) {
            $this->notificationService->notify(
                $chefMaster,
                'team_preferences_submitted',
                'Team preferences submitted',
                "Team '{$team->name}' has submitted their subject preferences",
                ['team_id' => $team->id]
            );
        }

        return redirect()->route('pfe.student.subjects.index')
            ->with('success', 'Preferences submitted successfully! Waiting for project assignment.');
    }

    /**
     * Show team's preference management
     */
    public function managePreferences(): View
    {
        $student = auth()->user();
        $team = $this->getCurrentTeam($student);

        if (!$team) {
            return redirect()->route('pfe.student.teams.index')
                ->withErrors(['error' => 'You need to be in a team to manage preferences.']);
        }

        $preferences = $team->subjectPreferences()
            ->with('subject.supervisor')
            ->orderBy('preference_order')
            ->get();

        $stats = [
            'total_preferences' => $preferences->count(),
            'max_allowed' => 5,
            'competition_analysis' => $this->analyzePreferenceCompetition($preferences),
            'submission_status' => $team->preferences_submitted_at ? 'submitted' : 'draft'
        ];

        return view('pfe.student.subjects.preferences', [
            'team' => $team,
            'preferences' => $preferences,
            'stats' => $stats,
            'can_modify' => $this->canModifyPreferences($team)
        ]);
    }

    /**
     * Get current team for student
     */
    private function getCurrentTeam($student): ?Team
    {
        $teamMember = TeamMember::where('user_id', $student->id)
            ->where('status', 'active')
            ->with('team')
            ->first();

        return $teamMember?->team;
    }

    /**
     * Check if team can select a subject
     */
    private function canSelectSubject(?Team $team, Subject $subject): bool
    {
        if (!$team || $team->status !== 'validated') {
            return false;
        }

        if ($subject->status !== 'published') {
            return false;
        }

        // Check if selection period is open
        $selectionDeadline = config('pfe.subject_selection_deadline');
        if ($selectionDeadline && now()->isAfter($selectionDeadline)) {
            return false;
        }

        // Check if team already has max preferences
        if ($team->subjectPreferences()->count() >= 5) {
            return false;
        }

        return true;
    }

    /**
     * Check if team can modify preferences
     */
    private function canModifyPreferences(?Team $team): bool
    {
        if (!$team) {
            return false;
        }

        // Can't modify after submission
        if ($team->preferences_submitted_at) {
            return false;
        }

        // Check deadline
        $modificationDeadline = config('pfe.preference_modification_deadline');
        if ($modificationDeadline && now()->isAfter($modificationDeadline)) {
            return false;
        }

        return true;
    }

    /**
     * Get competing teams for a subject
     */
    private function getCompetingTeams(Subject $subject)
    {
        return TeamSubjectPreference::where('subject_id', $subject->id)
            ->with(['team.leader', 'team.members'])
            ->orderBy('preference_order')
            ->get()
            ->map(function($preference) {
                return [
                    'team' => $preference->team,
                    'preference_order' => $preference->preference_order,
                    'selected_at' => $preference->selected_at
                ];
            });
    }

    /**
     * Calculate competition level for subject
     */
    private function calculateCompetitionLevel(Subject $subject): array
    {
        $totalInterested = $subject->teamPreferences()->count();
        $maxTeams = $subject->max_teams;

        $level = 'Low';
        $intensity = 0;

        if ($totalInterested > $maxTeams) {
            $ratio = $totalInterested / $maxTeams;
            if ($ratio > 3) {
                $level = 'Very High';
                $intensity = 90;
            } elseif ($ratio > 2) {
                $level = 'High';
                $intensity = 70;
            } else {
                $level = 'Moderate';
                $intensity = 50;
            }
        } else {
            $intensity = 20;
        }

        return [
            'level' => $level,
            'intensity' => $intensity,
            'total_interested' => $totalInterested,
            'max_teams' => $maxTeams,
            'ratio' => $maxTeams > 0 ? $totalInterested / $maxTeams : 0
        ];
    }

    /**
     * Find similar subjects
     */
    private function findSimilarSubjects(Subject $subject, $limit = 3)
    {
        $keywords = $subject->keywords ?? [];

        if (empty($keywords)) {
            return collect();
        }

        return Subject::where('status', 'published')
            ->where('id', '!=', $subject->id)
            ->where(function($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhereJsonContains('keywords', $keyword);
                }
            })
            ->with('supervisor')
            ->withCount('teamPreferences')
            ->take($limit)
            ->get();
    }

    /**
     * Get available departments
     */
    private function getAvailableDepartments(): array
    {
        return Subject::where('status', 'published')
            ->join('users', 'subjects.supervisor_id', '=', 'users.id')
            ->distinct()
            ->pluck('users.department')
            ->filter()
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Get popular keywords
     */
    private function getPopularKeywords(): array
    {
        $subjects = Subject::where('status', 'published')
            ->whereNotNull('keywords')
            ->get();

        $keywordCounts = [];

        foreach ($subjects as $subject) {
            if (is_array($subject->keywords)) {
                foreach ($subject->keywords as $keyword) {
                    $keywordCounts[$keyword] = ($keywordCounts[$keyword] ?? 0) + 1;
                }
            }
        }

        arsort($keywordCounts);
        return array_slice(array_keys($keywordCounts), 0, 20);
    }

    /**
     * Get selection statistics
     */
    private function getSelectionStats(Team $team): array
    {
        $totalTeams = Team::where('status', 'validated')->count();
        $teamsWithPreferences = Team::whereHas('subjectPreferences')->count();
        $submittedTeams = Team::whereNotNull('preferences_submitted_at')->count();

        return [
            'total_teams' => $totalTeams,
            'teams_with_preferences' => $teamsWithPreferences,
            'submitted_teams' => $submittedTeams,
            'selection_rate' => $totalTeams > 0 ? ($teamsWithPreferences / $totalTeams) * 100 : 0,
            'submission_rate' => $totalTeams > 0 ? ($submittedTeams / $totalTeams) * 100 : 0,
            'my_team_preferences' => $team->subjectPreferences()->count()
        ];
    }

    /**
     * Analyze preference competition
     */
    private function analyzePreferenceCompetition($preferences): array
    {
        $analysis = [];

        foreach ($preferences as $preference) {
            $subject = $preference->subject;
            $totalCompetitors = $subject->teamPreferences()->count();
            $maxTeams = $subject->max_teams;

            $competitionRatio = $maxTeams > 0 ? $totalCompetitors / $maxTeams : 0;

            $analysis[] = [
                'subject_id' => $subject->id,
                'subject_title' => $subject->title,
                'preference_order' => $preference->preference_order,
                'total_competitors' => $totalCompetitors,
                'max_teams' => $maxTeams,
                'competition_ratio' => $competitionRatio,
                'risk_level' => $competitionRatio > 2 ? 'high' : ($competitionRatio > 1.5 ? 'medium' : 'low')
            ];
        }

        return $analysis;
    }

    /**
     * Notify team members
     */
    private function notifyTeamMembers(Team $team, string $type, array $data): void
    {
        foreach ($team->members as $member) {
            if ($member->user_id !== auth()->id()) {
                $messages = [
                    'preference_added' => "Subject '{$data['subject_title']}' added as preference #{$data['preference_order']}",
                    'preference_removed' => "Subject '{$data['subject_title']}' removed from preferences",
                    'preferences_reordered' => "Team preferences have been reordered",
                    'preferences_submitted' => "Team preferences have been submitted for final review"
                ];

                $this->notificationService->notify(
                    $member->user,
                    $type,
                    'Team preferences updated',
                    $messages[$type] ?? 'Team preferences have been updated',
                    ['team_id' => $team->id]
                );
            }
        }
    }
}