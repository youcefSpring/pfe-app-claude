<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subject;
use App\Models\Team;
use App\Models\Project;
use App\Models\ExternalProject;

class WorkflowService
{
    protected SubjectService $subjectService;
    protected TeamService $teamService;
    protected ProjectService $projectService;
    protected ConflictService $conflictService;

    public function __construct(
        SubjectService $subjectService,
        TeamService $teamService,
        ProjectService $projectService,
        ConflictService $conflictService
    ) {
        $this->subjectService = $subjectService;
        $this->teamService = $teamService;
        $this->projectService = $projectService;
        $this->conflictService = $conflictService;
    }

    /**
     * Complete subject creation and submission workflow.
     */
    public function completeSubjectWorkflow(array $subjectData, User $teacher): Subject
    {
        // Create subject
        $subject = $this->subjectService->createSubject($subjectData, $teacher);

        // Auto-submit for validation if complete
        if ($this->isSubjectReadyForSubmission($subject)) {
            $this->subjectService->submitForValidation($subject);
        }

        return $subject;
    }

    /**
     * Complete team formation workflow.
     */
    public function completeTeamFormationWorkflow(
        array $teamData,
        User $creator,
        array $memberIds = []
    ): Team {
        // Create team
        $team = $this->teamService->createTeam($teamData, $creator);

        // Add members if specified
        foreach ($memberIds as $memberId) {
            $member = User::find($memberId);
            if ($member) {
                $this->teamService->addMemberToTeam($team, $member);
            }
        }

        // Check if team is now complete and update status
        if ($team->isComplete()) {
            $team->update(['status' => 'complete']);
        }

        return $team;
    }

    /**
     * Complete subject selection workflow with conflict handling.
     */
    public function completeSubjectSelectionWorkflow(Team $team, Subject $subject): array
    {
        // Validate selection
        if (!$this->subjectService->canBeSelectedByTeam($subject, $team)) {
            throw new \Exception('Subject cannot be selected by this team');
        }

        // Select subject
        $this->teamService->selectSubject($team, $subject);

        // Check for conflicts
        $conflict = $this->conflictService->detectConflict($subject, $team);

        $result = [
            'team' => $team,
            'subject' => $subject,
            'conflict_created' => $conflict !== null,
            'conflict' => $conflict,
            'next_steps' => [],
        ];

        if ($conflict) {
            $result['next_steps'][] = 'Wait for conflict resolution by department head';
            $result['next_steps'][] = 'Monitor conflict status in dashboard';
        } else {
            // No conflict, proceed with assignment
            $team->update(['status' => 'assigned']);
            $result['next_steps'][] = 'Team assigned to subject';
            $result['next_steps'][] = 'Project creation ready';
        }

        return $result;
    }

    /**
     * Complete project creation workflow.
     */
    public function completeProjectCreationWorkflow(Team $team): Project
    {
        // Validate team status
        if ($team->status !== 'assigned') {
            throw new \Exception('Team must be assigned to a subject before project creation');
        }

        // Determine project type and create
        if ($team->subject_id) {
            // Internal project
            $project = $this->projectService->createProject($team, $team->subject);
        } elseif ($team->externalProject) {
            // External project
            $project = $this->projectService->createProject($team, null, $team->externalProject);
        } else {
            throw new \Exception('Team must have either a subject or external project');
        }

        // Auto-start project if ready
        if ($this->isProjectReadyToStart($project)) {
            $this->projectService->startProject($project);
        }

        return $project;
    }

    /**
     * Complete external project submission workflow.
     */
    public function completeExternalProjectWorkflow(
        Team $team,
        array $externalProjectData
    ): ExternalProject {
        // Create external project
        $externalProject = $team->submitExternalProject($externalProjectData);

        // Auto-assign supervisor if criteria are met
        $supervisor = $this->findBestSupervisorForExternalProject($externalProject);
        if ($supervisor) {
            $externalProject->assignSupervisor($supervisor);

            // Update team status
            $team->update(['status' => 'assigned']);
        }

        return $externalProject;
    }

    /**
     * Process conflict resolution workflow.
     */
    public function processConflictResolutionWorkflow(
        int $conflictId,
        int $winningTeamId,
        User $resolver,
        string $notes = null
    ): array {
        $conflict = \App\Models\SubjectConflict::findOrFail($conflictId);
        $winningTeam = Team::findOrFail($winningTeamId);

        // Resolve conflict
        $this->conflictService->resolveConflict($conflict, $winningTeam, $resolver, $notes);

        // Get impact preview
        $impact = $this->conflictService->previewResolution($conflict, $winningTeam);

        // Create projects for winning team if ready
        if ($winningTeam->status === 'assigned') {
            $project = $this->completeProjectCreationWorkflow($winningTeam);
            $impact['project_created'] = $project;
        }

        return $impact;
    }

    /**
     * Process batch validation workflow.
     */
    public function processBatchValidationWorkflow(
        array $subjectIds,
        string $action,
        User $validator,
        string $feedback = null
    ): array {
        $results = [
            'processed' => [],
            'failed' => [],
            'summary' => [
                'total' => count($subjectIds),
                'successful' => 0,
                'failed' => 0,
            ]
        ];

        foreach ($subjectIds as $subjectId) {
            try {
                $subject = Subject::findOrFail($subjectId);
                $this->subjectService->validateSubject($subject, $validator, $action, $feedback);

                $results['processed'][] = [
                    'subject_id' => $subjectId,
                    'title' => $subject->title,
                    'status' => 'success',
                ];
                $results['summary']['successful']++;
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'subject_id' => $subjectId,
                    'error' => $e->getMessage(),
                ];
                $results['summary']['failed']++;
            }
        }

        return $results;
    }

    /**
     * Get workflow status for dashboard.
     */
    public function getWorkflowStatus(User $user): array
    {
        $status = [
            'role' => $user->role,
            'pending_actions' => [],
            'recent_activity' => [],
            'statistics' => [],
        ];

        switch ($user->role) {
            case 'student':
                $status = array_merge($status, $this->getStudentWorkflowStatus($user));
                break;

            case 'teacher':
                $status = array_merge($status, $this->getTeacherWorkflowStatus($user));
                break;

            case 'department_head':
                $status = array_merge($status, $this->getDepartmentHeadWorkflowStatus($user));
                break;

            case 'admin':
                $status = array_merge($status, $this->getAdminWorkflowStatus($user));
                break;
        }

        return $status;
    }

    /**
     * Helper methods for workflow validation.
     */
    private function isSubjectReadyForSubmission(Subject $subject): bool
    {
        return !empty($subject->title) &&
               !empty($subject->description) &&
               !empty($subject->keywords) &&
               !empty($subject->tools) &&
               !empty($subject->plan);
    }

    private function isProjectReadyToStart(Project $project): bool
    {
        return $project->status === 'assigned' &&
               $project->supervisor_id &&
               $project->team->status === 'active';
    }

    private function findBestSupervisorForExternalProject(ExternalProject $externalProject): ?User
    {
        // Find supervisor with matching expertise and availability
        $supervisors = User::where('role', 'teacher')
            ->whereHas('supervisedProjects', function ($q) {
                $q->whereIn('status', ['assigned', 'in_progress']);
            }, '<', 5)
            ->get();

        $bestMatch = null;
        $bestScore = 0;

        foreach ($supervisors as $supervisor) {
            $score = $supervisor->getSpecialtyMatchScore($externalProject->technologies ?? '');
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $supervisor;
            }
        }

        return $bestMatch;
    }

    private function getStudentWorkflowStatus(User $student): array
    {
        $team = $student->activeTeam();

        $status = [
            'has_team' => $team !== null,
            'team_status' => $team?->status,
            'pending_actions' => [],
        ];

        if (!$team) {
            $status['pending_actions'][] = 'Create or join a team';
        } elseif ($team->status === 'forming') {
            $issues = $this->teamService->validateTeamCompleteness($team);
            $status['pending_actions'] = array_merge($status['pending_actions'], $issues);
        } elseif ($team->status === 'complete' && !$team->subject_id) {
            $status['pending_actions'][] = 'Select a subject for your team';
        }

        return $status;
    }

    private function getTeacherWorkflowStatus(User $teacher): array
    {
        $pendingSubmissions = $teacher->supervisedProjects()
            ->whereHas('submissions', function ($q) {
                $q->where('status', 'submitted');
            })
            ->count();

        return [
            'pending_actions' => [
                "Review {$pendingSubmissions} pending submissions",
            ],
            'statistics' => [
                'subjects_created' => $teacher->subjects()->count(),
                'projects_supervised' => $teacher->supervisedProjects()->count(),
                'current_workload' => $teacher->getCurrentWorkload(),
            ],
        ];
    }

    private function getDepartmentHeadWorkflowStatus(User $departmentHead): array
    {
        $pendingSubjects = $this->subjectService->getSubjectsForValidation($departmentHead->department)->count();
        $pendingConflicts = $this->conflictService->getConflictsByDepartment($departmentHead->department)->count();

        return [
            'pending_actions' => [
                "Validate {$pendingSubjects} pending subjects",
                "Resolve {$pendingConflicts} subject conflicts",
            ],
            'statistics' => [
                'subjects_validated' => $departmentHead->validatedSubjects()->count(),
                'conflicts_resolved' => $departmentHead->resolvedConflicts()->count(),
            ],
        ];
    }

    private function getAdminWorkflowStatus(User $admin): array
    {
        $readyProjects = Project::readyForDefense()->count();

        return [
            'pending_actions' => [
                "Schedule defenses for {$readyProjects} ready projects",
            ],
            'statistics' => [
                'reports_generated' => $admin->generatedReports()->count(),
                'defenses_scheduled' => \App\Models\Defense::count(),
            ],
        ];
    }
}