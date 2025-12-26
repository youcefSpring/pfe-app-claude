<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Models\Subject;
use App\Models\ExternalProject;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Collection;

class ProjectService
{
    /**
     * Create project from team and subject assignment.
     */
    public function createProject(Team $team, Subject $subject = null, ExternalProject $externalProject = null): Project
    {
        // Validate team status
        if ($team->status !== 'assigned') {
            throw new \Exception('Team must be in assigned status to create project');
        }

        // Determine project type
        $type = $subject ? 'internal' : 'external';

        if ($type === 'internal' && !$subject) {
            throw new \Exception('Internal projects require a subject');
        }

        if ($type === 'external' && !$externalProject) {
            throw new \Exception('External projects require external project details');
        }

        // Auto-assign supervisor
        $supervisor = $this->assignBestSupervisor($team, $subject, $externalProject);

        $currentYear = AcademicYear::getCurrentYear();
        $project = Project::create([
            'team_id' => $team->id,
            'subject_id' => $subject?->id,
            'external_project_id' => $externalProject?->id,
            'supervisor_id' => $supervisor->id,
            'type' => $type,
            'status' => 'assigned',
            'academic_year' => $currentYear ? $currentYear->year : date('Y') . '-' . (date('Y') + 1),
        ]);

        // Update team status
        $team->update(['status' => 'active']);

        return $project;
    }

    /**
     * Start project (transition from assigned to in_progress).
     */
    public function startProject(Project $project): bool
    {
        if ($project->status !== 'assigned') {
            throw new \Exception('Project must be in assigned status to start');
        }

        $result = $project->start();

        // Notify team members
        foreach ($project->team->members as $member) {
            $member->user->notify(new \App\Notifications\ProjectAssigned($project, 'team'));
        }

        return $result;
    }

    /**
     * Submit project for defense.
     */
    public function submitProject(Project $project): bool
    {
        // Validate final report is approved
        $finalReport = $project->submissions()
            ->where('type', 'final_report')
            ->where('status', 'approved')
            ->first();

        if (!$finalReport) {
            throw new \Exception('Final report must be approved before project submission');
        }

        $result = $project->submit();

        if ($result) {
            // Notify defense committee (jury members)
            foreach ($project->defense->juries as $jury) {
                $jury->teacher->notify(new \App\Notifications\DefenseScheduled($project->defense, 'jury'));
            }
        }

        return $result;
    }

    /**
     * Assign supervisor to project.
     */
    public function assignSupervisor(Project $project, User $supervisor, User $coSupervisor = null): bool
    {
        // Validate supervisor
        if (!$supervisor->isTeacher() && !$supervisor->isExternalSupervisor()) {
            throw new \Exception('Supervisor must be a teacher or external supervisor');
        }

        // Check workload
        if ($supervisor->isTeacher() && !$supervisor->canSuperviseMoreProjects()) {
            throw new \Exception('Supervisor has reached maximum project capacity');
        }

        if ($coSupervisor) {
            if (!$coSupervisor->isTeacher() && !$coSupervisor->isExternalSupervisor()) {
                throw new \Exception('Co-supervisor must be a teacher or external supervisor');
            }
        }

        return $project->assignSupervisor($supervisor, $coSupervisor);
    }

    /**
     * Get projects for a supervisor.
     */
    public function getProjectsBySupervisor(User $supervisor): Collection
    {
        if (!$supervisor->isTeacher() && !$supervisor->isExternalSupervisor()) {
            throw new \Exception('User is not a supervisor');
        }

        return Project::where('supervisor_id', $supervisor->id)
            ->orWhere('co_supervisor_id', $supervisor->id)
            ->with(['team.members.student', 'subject', 'externalProject'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get projects for a team.
     */
    public function getProjectsByTeam(Team $team): Collection
    {
        return Project::where('team_id', $team->id)
            ->with(['supervisor', 'subject', 'externalProject'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get projects ready for defense.
     */
    public function getProjectsReadyForDefense(): Collection
    {
        return Project::readyForDefense()
            ->with(['team.members.student', 'supervisor', 'subject'])
            ->orderBy('submitted_at', 'asc')
            ->get();
    }

    /**
     * Auto-assign best supervisor for project.
     */
    private function assignBestSupervisor(Team $team, Subject $subject = null, ExternalProject $externalProject = null): User
    {
        // For internal projects, prefer subject creator if available
        if ($subject && $subject->teacher->canSuperviseMoreProjects()) {
            return $subject->teacher;
        }

        // For external projects, find supervisor with matching expertise
        if ($externalProject) {
            $supervisor = $this->findSupervisorByExpertise($externalProject->technologies);
            if ($supervisor) {
                return $supervisor;
            }
        }

        // Fallback: find any available supervisor
        $availableSupervisor = User::where('role', 'teacher')
            ->whereHas('supervisedProjects', function ($q) {
                $q->whereIn('status', ['assigned', 'in_progress']);
            }, '<', 5) // Max 5 projects per supervisor
            ->first();

        if (!$availableSupervisor) {
            throw new \Exception('No available supervisors found');
        }

        return $availableSupervisor;
    }

    /**
     * Find supervisor with matching expertise.
     */
    private function findSupervisorByExpertise(string $technologies = null): ?User
    {
        if (!$technologies) {
            return null;
        }

        // Simple keyword matching - in real implementation, use more sophisticated matching
        $supervisors = User::where('role', 'teacher')
            ->whereHas('supervisedProjects', function ($q) {
                $q->whereIn('status', ['assigned', 'in_progress']);
            }, '<', 5)
            ->get();

        $bestMatch = null;
        $bestScore = 0;

        foreach ($supervisors as $supervisor) {
            $score = $supervisor->getSpecialtyMatchScore($technologies);
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $supervisor;
            }
        }

        return $bestMatch;
    }

    /**
     * Get project statistics.
     */
    public function getProjectStatistics(): array
    {
        return [
            'total' => Project::count(),
            'assigned' => Project::where('status', 'assigned')->count(),
            'in_progress' => Project::where('status', 'in_progress')->count(),
            'submitted' => Project::where('status', 'submitted')->count(),
            'defended' => Project::where('status', 'defended')->count(),
            'internal' => Project::internal()->count(),
            'external' => Project::external()->count(),
            'ready_for_defense' => Project::readyForDefense()->count(),
        ];
    }

    /**
     * Get supervisor workload distribution.
     */
    public function getSupervisorWorkloadDistribution(): Collection
    {
        return User::where('role', 'teacher')
            ->withCount(['supervisedProjects' => function ($q) {
                $q->whereIn('status', ['assigned', 'in_progress']);
            }])
            ->orderBy('supervised_projects_count', 'desc')
            ->get();
    }

    /**
     * Handle submission for project.
     */
    public function handleSubmission(Project $project, array $submissionData): Submission
    {
        if (!$project->canSubmitDeliverable()) {
            throw new \Exception('Project cannot accept submissions at this time');
        }

        $submission = $project->submissions()->create([
            'type' => $submissionData['type'],
            'title' => $submissionData['title'],
            'description' => $submissionData['description'] ?? null,
            'file_path' => $submissionData['file_path'] ?? null,
            'submission_date' => now(),
            'status' => 'submitted',
        ]);

        // Notify supervisor
        $supervisor->notify(new \App\Notifications\ProjectAssigned($project, 'supervisor'));

        return $submission;
    }

    /**
     * Review submission.
     */
    public function reviewSubmission(
        Submission $submission,
        User $reviewer,
        string $action,
        string $feedback = null
    ): bool {
        // Validate reviewer is supervisor of the project
        if ($submission->project->supervisor_id !== $reviewer->id &&
            $submission->project->co_supervisor_id !== $reviewer->id) {
            throw new \Exception('Only project supervisors can review submissions');
        }

        switch ($action) {
            case 'approve':
                return $submission->approve($reviewer, $feedback);

            case 'reject':
                return $submission->reject($reviewer, $feedback);

            default:
                throw new \Exception('Invalid review action');
        }
    }

    /**
     * Get project timeline/progress.
     */
    public function getProjectTimeline(Project $project): array
    {
        $timeline = [
            [
                'event' => 'Project Created',
                'date' => $project->created_at,
                'status' => 'completed',
                'description' => 'Project was created and assigned to team',
            ]
        ];

        if ($project->started_at) {
            $timeline[] = [
                'event' => 'Project Started',
                'date' => $project->started_at,
                'status' => 'completed',
                'description' => 'Team began working on the project',
            ];
        }

        // Add submission events
        foreach ($project->submissions()->orderBy('submission_date')->get() as $submission) {
            $timeline[] = [
                'event' => "Submission: {$submission->title}",
                'date' => $submission->submission_date,
                'status' => $submission->status === 'approved' ? 'completed' : 'pending',
                'description' => $submission->description ?? "Submitted {$submission->type}",
            ];
        }

        if ($project->submitted_at) {
            $timeline[] = [
                'event' => 'Project Submitted',
                'date' => $project->submitted_at,
                'status' => 'completed',
                'description' => 'Project was submitted for defense',
            ];
        }

        // Add defense if scheduled
        if ($project->defense) {
            $timeline[] = [
                'event' => 'Defense Scheduled',
                'date' => $project->defense->defense_date,
                'status' => $project->defense->status === 'completed' ? 'completed' : 'pending',
                'description' => "Defense scheduled for {$project->defense->defense_date->format('M d, Y')}",
            ];
        }

        return $timeline;
    }
}