<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\Team;
use App\Models\PfeProject;
use App\Models\Defense;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportingService
{
    public function generateDashboardStats(User $user): array
    {
        switch (true) {
            case $user->hasRole('student'):
                return $this->getStudentStats($user);
            case $user->hasRole('teacher'):
                return $this->getTeacherStats($user);
            case $user->hasRole('chef_master'):
                return $this->getDepartmentHeadStats($user);
            case $user->hasRole('admin_pfe'):
                return $this->getAdminStats();
            default:
                return [];
        }
    }

    public function generateSubjectReport(array $filters = []): array
    {
        $query = Subject::query();

        // Apply filters
        if (isset($filters['department'])) {
            $query->whereHas('supervisor', function ($q) use ($filters) {
                $q->where('department', $filters['department']);
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $subjects = $query->with(['supervisor', 'projects.team'])->get();

        return [
            'total_subjects' => $subjects->count(),
            'by_status' => $subjects->groupBy('status')->map->count(),
            'by_department' => $subjects->groupBy('supervisor.department')->map->count(),
            'assigned_subjects' => $subjects->filter(fn($s) => $s->projects->isNotEmpty())->count(),
            'subjects' => $subjects->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'title' => $subject->title,
                    'supervisor' => $subject->supervisor->first_name . ' ' . $subject->supervisor->last_name,
                    'department' => $subject->supervisor->department,
                    'status' => $subject->status,
                    'assigned_team' => $subject->projects->first()?->team?->name,
                    'created_at' => $subject->created_at->format('Y-m-d')
                ];
            })
        ];
    }

    public function generateTeamReport(array $filters = []): array
    {
        $query = Team::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['department'])) {
            $query->whereHas('leader', function ($q) use ($filters) {
                $q->where('department', $filters['department']);
            });
        }

        $teams = $query->with(['leader', 'members.user', 'project.subject'])->get();

        return [
            'total_teams' => $teams->count(),
            'by_status' => $teams->groupBy('status')->map->count(),
            'by_size' => $teams->groupBy('size')->map->count(),
            'assigned_teams' => $teams->filter(fn($t) => $t->project)->count(),
            'average_size' => $teams->avg('size'),
            'teams' => $teams->map(function ($team) {
                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'leader' => $team->leader->first_name . ' ' . $team->leader->last_name,
                    'size' => $team->size,
                    'status' => $team->status,
                    'assigned_project' => $team->project?->subject?->title,
                    'formation_date' => $team->formation_completed_at?->format('Y-m-d')
                ];
            })
        ];
    }

    public function generateDefenseReport(array $filters = []): array
    {
        $query = Defense::query();

        if (isset($filters['date_from'])) {
            $query->where('defense_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('defense_date', '<=', $filters['date_to']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $defenses = $query->with([
            'project.subject',
            'project.team',
            'room',
            'juryPresident',
            'juryExaminer',
            'jurySupervisor'
        ])->get();

        return [
            'total_defenses' => $defenses->count(),
            'by_status' => $defenses->groupBy('status')->map->count(),
            'completed_defenses' => $defenses->where('status', 'completed')->count(),
            'average_grade' => $defenses->where('final_grade', '!=', null)->avg('final_grade'),
            'grade_distribution' => $this->calculateGradeDistribution($defenses),
            'defenses' => $defenses->map(function ($defense) {
                return [
                    'id' => $defense->id,
                    'project_title' => $defense->project->subject->title,
                    'team_name' => $defense->project->team->name,
                    'defense_date' => $defense->defense_date,
                    'start_time' => $defense->start_time,
                    'room' => $defense->room->name,
                    'status' => $defense->status,
                    'final_grade' => $defense->final_grade,
                    'jury_president' => $defense->juryPresident->first_name . ' ' . $defense->juryPresident->last_name
                ];
            })
        ];
    }

    public function generateSupervisorWorkloadReport(): array
    {
        $supervisors = User::role('teacher')
            ->withCount(['supervisedSubjects', 'supervisedPfeProjects', 'supervisedDefenses'])
            ->get();

        return [
            'total_supervisors' => $supervisors->count(),
            'average_projects' => $supervisors->avg('supervised_pfe_projects_count'),
            'max_projects' => $supervisors->max('supervised_pfe_projects_count'),
            'workload_distribution' => $supervisors->groupBy(function ($supervisor) {
                $count = $supervisor->supervised_pfe_projects_count;
                if ($count == 0) return '0 projects';
                if ($count <= 3) return '1-3 projects';
                if ($count <= 6) return '4-6 projects';
                if ($count <= 8) return '7-8 projects';
                return '9+ projects';
            })->map->count(),
            'supervisors' => $supervisors->map(function ($supervisor) {
                return [
                    'id' => $supervisor->id,
                    'name' => $supervisor->first_name . ' ' . $supervisor->last_name,
                    'department' => $supervisor->department,
                    'subjects_count' => $supervisor->supervised_subjects_count,
                    'projects_count' => $supervisor->supervised_pfe_projects_count,
                    'defenses_count' => $supervisor->supervised_defenses_count
                ];
            })->sortByDesc('projects_count')->values()
        ];
    }

    public function generateProgressReport(array $filters = []): array
    {
        $projects = PfeProject::with(['subject', 'team', 'deliverables'])
            ->when(isset($filters['supervisor_id']), function ($q) use ($filters) {
                $q->where('supervisor_id', $filters['supervisor_id']);
            })
            ->get();

        return [
            'total_projects' => $projects->count(),
            'by_status' => $projects->groupBy('status')->map->count(),
            'projects_with_deliverables' => $projects->filter(fn($p) => $p->deliverables->isNotEmpty())->count(),
            'overdue_projects' => $projects->filter(function ($project) {
                return $project->expected_end_date < now() && !in_array($project->status, ['completed', 'defended']);
            })->count(),
            'progress_details' => $projects->map(function ($project) {
                $progress = $this->calculateProjectProgress($project);
                return [
                    'id' => $project->id,
                    'title' => $project->subject->title,
                    'team' => $project->team->name,
                    'status' => $project->status,
                    'progress_percentage' => $progress,
                    'expected_end_date' => $project->expected_end_date,
                    'is_overdue' => $project->expected_end_date < now() && !in_array($project->status, ['completed', 'defended']),
                    'deliverables_count' => $project->deliverables->count()
                ];
            })
        ];
    }

    public function exportToExcel(string $reportType, array $data, array $filters = []): string
    {
        // This would integrate with a library like PhpSpreadsheet
        // For now, return a placeholder file path
        $fileName = "{$reportType}_export_" . now()->format('Y-m-d_H-i-s') . '.xlsx';
        $filePath = "exports/{$fileName}";

        // Generate Excel file (placeholder)
        // Implementation would use PhpSpreadsheet or similar

        return $filePath;
    }

    private function getStudentStats(User $student): array
    {
        $teamMembership = $student->teamMemberships()->with('team.project')->first();

        if (!$teamMembership) {
            return [
                'has_team' => false,
                'message' => 'You need to join or create a team'
            ];
        }

        $team = $teamMembership->team;
        $project = $team->project;

        return [
            'has_team' => true,
            'team_name' => $team->name,
            'team_status' => $team->status,
            'team_size' => $team->size,
            'has_project' => $project !== null,
            'project_title' => $project?->subject?->title,
            'project_status' => $project?->status,
            'supervisor' => $project?->supervisor?->first_name . ' ' . $project?->supervisor?->last_name,
            'deliverables_submitted' => $project?->deliverables?->count() ?? 0,
            'defense_scheduled' => $project?->defense !== null,
            'defense_date' => $project?->defense?->defense_date
        ];
    }

    private function getTeacherStats(User $teacher): array
    {
        return [
            'subjects_created' => $teacher->supervisedSubjects()->count(),
            'subjects_published' => $teacher->supervisedSubjects()->where('status', 'published')->count(),
            'projects_supervised' => $teacher->supervisedPfeProjects()->count(),
            'defenses_to_supervise' => $teacher->supervisedDefenses()->where('status', 'scheduled')->count(),
            'defenses_completed' => $teacher->supervisedDefenses()->where('status', 'completed')->count(),
            'recent_projects' => $teacher->supervisedPfeProjects()
                ->with(['subject', 'team'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($project) {
                    return [
                        'title' => $project->subject->title,
                        'team' => $project->team->name,
                        'status' => $project->status
                    ];
                })
        ];
    }

    private function getDepartmentHeadStats(User $head): array
    {
        $department = $head->department;

        return [
            'pending_validations' => Subject::where('status', 'submitted')
                ->whereHas('supervisor', fn($q) => $q->where('department', $department))
                ->count(),
            'teachers_count' => User::role('teacher')->where('department', $department)->count(),
            'students_count' => User::role('student')->where('department', $department)->count(),
            'active_projects' => PfeProject::whereHas('supervisor', fn($q) => $q->where('department', $department))
                ->whereNotIn('status', ['completed'])
                ->count(),
            'completed_projects' => PfeProject::whereHas('supervisor', fn($q) => $q->where('department', $department))
                ->where('status', 'completed')
                ->count()
        ];
    }

    private function getAdminStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_subjects' => Subject::count(),
            'total_teams' => Team::count(),
            'total_projects' => PfeProject::count(),
            'scheduled_defenses' => Defense::where('status', 'scheduled')->count(),
            'completed_defenses' => Defense::where('status', 'completed')->count(),
            'system_health' => [
                'pending_validations' => Subject::where('status', 'submitted')->count(),
                'teams_without_projects' => Team::where('status', 'validated')->whereDoesntHave('project')->count(),
                'overdue_projects' => PfeProject::where('expected_end_date', '<', now())
                    ->whereNotIn('status', ['completed', 'defended'])
                    ->count()
            ]
        ];
    }

    private function calculateGradeDistribution(Collection $defenses): array
    {
        $completedDefenses = $defenses->where('final_grade', '!=', null);

        return [
            'excellent' => $completedDefenses->where('final_grade', '>=', 16)->count(),
            'very_good' => $completedDefenses->whereBetween('final_grade', [14, 16])->count(),
            'good' => $completedDefenses->whereBetween('final_grade', [12, 14])->count(),
            'satisfactory' => $completedDefenses->whereBetween('final_grade', [10, 12])->count(),
            'insufficient' => $completedDefenses->where('final_grade', '<', 10)->count()
        ];
    }

    private function calculateProjectProgress(PfeProject $project): int
    {
        $statusProgress = [
            'assigned' => 10,
            'in_progress' => 30,
            'under_review' => 60,
            'needs_revision' => 50,
            'ready_for_defense' => 80,
            'defended' => 95,
            'completed' => 100
        ];

        $baseProgress = $statusProgress[$project->status] ?? 0;

        // Add bonus for deliverables
        $deliverablesBonus = min($project->deliverables->count() * 5, 20);

        return min($baseProgress + $deliverablesBonus, 100);
    }
}