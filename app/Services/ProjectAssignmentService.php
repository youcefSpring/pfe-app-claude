<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\Team;
use App\Models\TeamSubjectPreference;
use App\Models\PfeProject;
use App\Models\User;
use App\Models\PfeNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ProjectAssignmentService
{
    public function addTeamPreference(Team $team, Subject $subject, int $order): TeamSubjectPreference
    {
        $this->validatePreferenceAddition($team, $subject, $order);

        return TeamSubjectPreference::create([
            'team_id' => $team->id,
            'subject_id' => $subject->id,
            'preference_order' => $order
        ]);
    }

    public function assignProjects(): array
    {
        return DB::transaction(function () {
            $assignments = [];
            $conflicts = [];

            // Get all validated teams and published subjects
            $teams = Team::where('status', 'validated')->with('subjectPreferences.subject')->get();
            $subjects = Subject::where('status', 'published')->get();

            // Phase 1: Automatic assignment using algorithm
            $automaticAssignments = $this->performAutomaticAssignment($teams, $subjects);
            $assignments = array_merge($assignments, $automaticAssignments['assignments']);
            $conflicts = array_merge($conflicts, $automaticAssignments['conflicts']);

            // Phase 2: Handle remaining unassigned teams and subjects
            $this->handleUnassignedEntities($teams, $subjects, $assignments);

            return [
                'assignments' => $assignments,
                'conflicts' => $conflicts,
                'success_rate' => count($assignments) / max(count($teams), 1) * 100
            ];
        });
    }

    public function resolveConflict(array $conflictData, Team $selectedTeam): PfeProject
    {
        $subject = Subject::findOrFail($conflictData['subject_id']);

        return DB::transaction(function () use ($subject, $selectedTeam, $conflictData) {
            // Create project assignment
            $project = $this->createProjectAssignment($selectedTeam, $subject);

            // Notify all involved teams
            foreach ($conflictData['competing_teams'] as $teamId) {
                $team = Team::find($teamId);
                if ($team && $team->id !== $selectedTeam->id) {
                    $this->notifyConflictResolution($team, $subject, false);
                }
            }

            $this->notifyConflictResolution($selectedTeam, $subject, true);

            return $project;
        });
    }

    public function assignExternalProject(Team $team, array $projectData): PfeProject
    {
        $this->validateExternalProjectData($projectData);

        return DB::transaction(function () use ($team, $projectData) {
            // Create external subject
            $subject = Subject::create([
                'title' => $projectData['title'],
                'description' => $projectData['description'],
                'keywords' => $projectData['keywords'] ?? [],
                'max_teams' => 1,
                'supervisor_id' => $this->assignSupervisor($team, $projectData),
                'external_supervisor' => $projectData['external_supervisor'] ?? null,
                'external_company' => $projectData['external_company'] ?? null,
                'status' => 'approved'
            ]);

            // Create project assignment
            return $this->createProjectAssignment($team, $subject);
        });
    }

    private function performAutomaticAssignment(Collection $teams, Collection $subjects): array
    {
        $assignments = [];
        $conflicts = [];
        $assignedSubjects = [];

        // Sort teams by average grade (if available) and preference timestamp
        $sortedTeams = $teams->sortByDesc(function ($team) {
            return $this->calculateTeamScore($team);
        });

        foreach ($sortedTeams as $team) {
            $preferences = $team->subjectPreferences()
                ->with('subject')
                ->orderBy('preference_order')
                ->get();

            $assigned = false;

            foreach ($preferences as $preference) {
                $subject = $preference->subject;

                // Skip if subject is already assigned
                if (in_array($subject->id, $assignedSubjects)) {
                    // Check for conflicts
                    $this->addToConflicts($conflicts, $subject, $team);
                    continue;
                }

                // Assign subject to team
                $project = $this->createProjectAssignment($team, $subject);
                $assignments[] = $project;
                $assignedSubjects[] = $subject->id;
                $assigned = true;
                break;
            }

            if (!$assigned) {
                // Team has no available preferences
                $this->notifyTeamNoAssignment($team);
            }
        }

        return [
            'assignments' => $assignments,
            'conflicts' => $conflicts
        ];
    }

    private function createProjectAssignment(Team $team, Subject $subject): PfeProject
    {
        $project = PfeProject::create([
            'subject_id' => $subject->id,
            'team_id' => $team->id,
            'supervisor_id' => $subject->supervisor_id,
            'external_supervisor' => $subject->external_supervisor,
            'external_company' => $subject->external_company,
            'status' => 'assigned',
            'start_date' => now()->addDays(7), // Start next week
            'expected_end_date' => now()->addMonths(6), // 6 months duration
        ]);

        // Update team status
        $team->update(['status' => 'assigned']);

        $this->notifyProjectAssigned($project);

        return $project;
    }

    private function calculateTeamScore(Team $team): float
    {
        // Base score calculation
        $score = 0;

        // Factor 1: Team formation timestamp (earlier = higher score)
        if ($team->formation_completed_at) {
            $daysAgo = now()->diffInDays($team->formation_completed_at);
            $score += max(0, 100 - $daysAgo); // Max 100 points for early formation
        }

        // Factor 2: Number of preferences (more preferences = higher score)
        $preferencesCount = $team->subjectPreferences()->count();
        $score += $preferencesCount * 10;

        // Factor 3: Team size (optimal size gets bonus)
        if ($team->size === 3) { // Optimal team size
            $score += 20;
        }

        return $score;
    }

    private function assignSupervisor(Team $team, array $projectData): int
    {
        // Find supervisor based on domain and workload balance
        $supervisors = User::role('teacher')
            ->where('department', $team->leader->department)
            ->withCount('supervisedPfeProjects')
            ->get();

        // Find supervisor with least workload
        $supervisor = $supervisors->sortBy('supervised_pfe_projects_count')->first();

        if (!$supervisor) {
            throw ValidationException::withMessages([
                'supervisor' => 'No available supervisor found for this department'
            ]);
        }

        return $supervisor->id;
    }

    private function addToConflicts(array &$conflicts, Subject $subject, Team $team): void
    {
        $conflictKey = "subject_{$subject->id}";

        if (!isset($conflicts[$conflictKey])) {
            $conflicts[$conflictKey] = [
                'subject_id' => $subject->id,
                'subject_title' => $subject->title,
                'competing_teams' => []
            ];
        }

        $conflicts[$conflictKey]['competing_teams'][] = [
            'team_id' => $team->id,
            'team_name' => $team->name,
            'score' => $this->calculateTeamScore($team)
        ];
    }

    private function validatePreferenceAddition(Team $team, Subject $subject, int $order): void
    {
        if ($team->status !== 'validated') {
            throw ValidationException::withMessages([
                'team' => 'Only validated teams can add preferences'
            ]);
        }

        if ($subject->status !== 'published') {
            throw ValidationException::withMessages([
                'subject' => 'Subject is not available for selection'
            ]);
        }

        if ($order < 1 || $order > 5) {
            throw ValidationException::withMessages([
                'order' => 'Preference order must be between 1 and 5'
            ]);
        }

        // Check if preference already exists
        if (TeamSubjectPreference::where('team_id', $team->id)
            ->where('subject_id', $subject->id)
            ->exists()) {
            throw ValidationException::withMessages([
                'preference' => 'Preference for this subject already exists'
            ]);
        }
    }

    private function validateExternalProjectData(array $data): void
    {
        $required = ['title', 'description', 'external_supervisor', 'external_company'];

        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw ValidationException::withMessages([
                    $field => "The {$field} field is required for external projects"
                ]);
            }
        }
    }

    private function handleUnassignedEntities(Collection $teams, Collection $subjects, array $assignments): void
    {
        $assignedTeamIds = collect($assignments)->pluck('team_id');
        $unassignedTeams = $teams->whereNotIn('id', $assignedTeamIds);

        foreach ($unassignedTeams as $team) {
            $this->notifyTeamNoAssignment($team);
        }
    }

    private function notifyProjectAssigned(PfeProject $project): void
    {
        // Notify team members
        $teamMembers = $project->team->members()->pluck('user_id');
        foreach ($teamMembers as $userId) {
            PfeNotification::create([
                'user_id' => $userId,
                'type' => 'project_assigned',
                'title' => 'Project Assigned',
                'message' => "Your team has been assigned the project: {$project->subject->title}",
                'data' => ['project_id' => $project->id]
            ]);
        }

        // Notify supervisor
        PfeNotification::create([
            'user_id' => $project->supervisor_id,
            'type' => 'project_assigned',
            'title' => 'New Project to Supervise',
            'message' => "You have been assigned to supervise: {$project->subject->title}",
            'data' => ['project_id' => $project->id]
        ]);
    }

    private function notifyConflictResolution(Team $team, Subject $subject, bool $selected): void
    {
        $type = $selected ? 'conflict_resolved_success' : 'conflict_resolved_failed';
        $message = $selected
            ? "Your team has been selected for: {$subject->title}"
            : "Another team was selected for: {$subject->title}";

        $teamMembers = $team->members()->pluck('user_id');
        foreach ($teamMembers as $userId) {
            PfeNotification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => 'Conflict Resolution',
                'message' => $message,
                'data' => ['subject_id' => $subject->id, 'team_id' => $team->id]
            ]);
        }
    }

    private function notifyTeamNoAssignment(Team $team): void
    {
        $teamMembers = $team->members()->pluck('user_id');
        foreach ($teamMembers as $userId) {
            PfeNotification::create([
                'user_id' => $userId,
                'type' => 'no_assignment',
                'title' => 'No Project Assignment',
                'message' => 'Your team was not assigned to any preferred subjects. Please contact administration.',
                'data' => ['team_id' => $team->id]
            ]);
        }
    }
}