<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subject;
use App\Models\Team;
use App\Models\Project;
use App\Models\Defense;
use App\Models\SubjectConflict;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationHelper
{
    /**
     * Send subject validation notification.
     */
    public static function sendSubjectValidationNotification(Subject $subject, string $action): void
    {
        $teacher = $subject->teacher;
        $validator = $subject->validator;

        $data = [
            'subject' => $subject,
            'teacher' => $teacher,
            'validator' => $validator,
            'action' => $action,
        ];

        try {
            // Send to subject creator
            self::sendEmail($teacher->email, "Subject {$action}: {$subject->title}", 'emails.subject-validation', $data);

            // Log notification
            Log::info("Subject validation notification sent", [
                'subject_id' => $subject->id,
                'action' => $action,
                'teacher_email' => $teacher->email,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send subject validation notification", [
                'subject_id' => $subject->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send conflict resolution notification.
     */
    public static function sendConflictResolutionNotification(SubjectConflict $conflict, Team $winningTeam): void
    {
        $subject = $conflict->subject;
        $allTeams = $conflict->teams;

        foreach ($allTeams as $team) {
            $isWinner = $team->id === $winningTeam->id;
            $members = $team->members()->with('student')->get();

            foreach ($members as $member) {
                $data = [
                    'conflict' => $conflict,
                    'subject' => $subject,
                    'team' => $team,
                    'student' => $member->student,
                    'is_winner' => $isWinner,
                    'winning_team' => $winningTeam,
                ];

                try {
                    $emailSubject = $isWinner
                        ? "Conflict Resolved - Subject Assigned: {$subject->title}"
                        : "Conflict Resolved - Subject Not Assigned: {$subject->title}";

                    self::sendEmail(
                        $member->student->email,
                        $emailSubject,
                        'emails.conflict-resolution',
                        $data
                    );
                } catch (\Exception $e) {
                    Log::error("Failed to send conflict resolution notification", [
                        'conflict_id' => $conflict->id,
                        'student_email' => $member->student->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Send defense scheduling notification.
     */
    public static function sendDefenseSchedulingNotification(Defense $defense): void
    {
        $project = $defense->project;
        $team = $project->team;
        $jury = $defense->jury()->with('teacher')->get();

        // Notify team members
        foreach ($team->members()->with('student')->get() as $member) {
            $data = [
                'defense' => $defense,
                'project' => $project,
                'team' => $team,
                'student' => $member->student,
                'jury' => $jury,
            ];

            try {
                self::sendEmail(
                    $member->student->email,
                    "Defense Scheduled: {$defense->defense_date->format('M d, Y')}",
                    'emails.defense-scheduled-student',
                    $data
                );
            } catch (\Exception $e) {
                Log::error("Failed to send defense notification to student", [
                    'defense_id' => $defense->id,
                    'student_email' => $member->student->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Notify jury members
        foreach ($jury as $juryMember) {
            $data = [
                'defense' => $defense,
                'project' => $project,
                'team' => $team,
                'jury_member' => $juryMember,
                'teacher' => $juryMember->teacher,
            ];

            try {
                self::sendEmail(
                    $juryMember->teacher->email,
                    "Jury Assignment: Defense on {$defense->defense_date->format('M d, Y')}",
                    'emails.defense-scheduled-jury',
                    $data
                );
            } catch (\Exception $e) {
                Log::error("Failed to send defense notification to jury member", [
                    'defense_id' => $defense->id,
                    'teacher_email' => $juryMember->teacher->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send project assignment notification.
     */
    public static function sendProjectAssignmentNotification(Project $project): void
    {
        $team = $project->team;
        $supervisor = $project->supervisor;

        // Notify team members
        foreach ($team->members()->with('student')->get() as $member) {
            $data = [
                'project' => $project,
                'team' => $team,
                'student' => $member->student,
                'supervisor' => $supervisor,
            ];

            try {
                self::sendEmail(
                    $member->student->email,
                    "Project Assigned: {$project->getTitle()}",
                    'emails.project-assignment-student',
                    $data
                );
            } catch (\Exception $e) {
                Log::error("Failed to send project assignment notification to student", [
                    'project_id' => $project->id,
                    'student_email' => $member->student->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Notify supervisor
        $data = [
            'project' => $project,
            'team' => $team,
            'supervisor' => $supervisor,
        ];

        try {
            self::sendEmail(
                $supervisor->email,
                "New Project Assignment: {$project->getTitle()}",
                'emails.project-assignment-supervisor',
                $data
            );
        } catch (\Exception $e) {
            Log::error("Failed to send project assignment notification to supervisor", [
                'project_id' => $project->id,
                'supervisor_email' => $supervisor->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send submission review notification.
     */
    public static function sendSubmissionReviewNotification(\App\Models\Submission $submission): void
    {
        $project = $submission->project;
        $team = $project->team;

        foreach ($team->members()->with('student')->get() as $member) {
            $data = [
                'submission' => $submission,
                'project' => $project,
                'team' => $team,
                'student' => $member->student,
                'reviewer' => $submission->reviewer,
            ];

            $status = $submission->status === 'approved' ? 'Approved' : 'Requires Revision';

            try {
                self::sendEmail(
                    $member->student->email,
                    "Submission {$status}: {$submission->title}",
                    'emails.submission-review',
                    $data
                );
            } catch (\Exception $e) {
                Log::error("Failed to send submission review notification", [
                    'submission_id' => $submission->id,
                    'student_email' => $member->student->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send deadline reminder notifications.
     */
    public static function sendDeadlineReminders(): void
    {
        // Send submission deadline reminders
        self::sendSubmissionDeadlineReminders();

        // Send defense preparation reminders
        self::sendDefensePreparationReminders();

        // Send validation deadline reminders
        self::sendValidationDeadlineReminders();
    }

    /**
     * Send batch notifications for department heads.
     */
    public static function sendDepartmentSummaryNotification(User $departmentHead): void
    {
        if (!$departmentHead->isDepartmentHead()) {
            return;
        }

        $department = $departmentHead->department;

        // Get summary data
        $pendingSubjects = Subject::pendingValidation()
            ->whereHas('teacher', function ($q) use ($department) {
                $q->where('department', $department);
            })->count();

        $pendingConflicts = SubjectConflict::pending()
            ->whereHas('subject.teacher', function ($q) use ($department) {
                $q->where('department', $department);
            })->count();

        $data = [
            'department_head' => $departmentHead,
            'department' => $department,
            'pending_subjects' => $pendingSubjects,
            'pending_conflicts' => $pendingConflicts,
            'date' => now()->format('M d, Y'),
        ];

        try {
            self::sendEmail(
                $departmentHead->email,
                "Daily Summary - {$department} Department",
                'emails.department-summary',
                $data
            );
        } catch (\Exception $e) {
            Log::error("Failed to send department summary notification", [
                'department_head_email' => $departmentHead->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send bulk notifications.
     */
    public static function sendBulkNotification(array $recipients, string $subject, string $template, array $data): array
    {
        $results = [
            'sent' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($recipients as $recipient) {
            try {
                self::sendEmail($recipient, $subject, $template, array_merge($data, ['recipient' => $recipient]));
                $results['sent']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'recipient' => $recipient,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Private helper methods.
     */
    private static function sendEmail(string $to, string $subject, string $template, array $data): void
    {
        // In a real implementation, this would use Laravel's Mail facade
        // For now, simulate email sending with logging

        Log::info("Email notification sent", [
            'to' => $to,
            'subject' => $subject,
            'template' => $template,
            'timestamp' => now(),
        ]);

        // TODO: Implement actual email sending
        // Mail::send($template, $data, function ($message) use ($to, $subject) {
        //     $message->to($to)->subject($subject);
        // });
    }

    private static function sendSubmissionDeadlineReminders(): void
    {
        // Find projects with upcoming submission deadlines
        $projects = Project::where('status', 'in_progress')
            ->whereDoesntHave('submissions', function ($q) {
                $q->where('type', 'final_report')->where('status', 'approved');
            })
            ->where('started_at', '<=', now()->subMonths(5)) // 5 months into project
            ->with('team.members.student')
            ->get();

        foreach ($projects as $project) {
            foreach ($project->team->members as $member) {
                $data = [
                    'project' => $project,
                    'student' => $member->student,
                ];

                try {
                    self::sendEmail(
                        $member->student->email,
                        "Submission Deadline Reminder",
                        'emails.submission-deadline-reminder',
                        $data
                    );
                } catch (\Exception $e) {
                    Log::error("Failed to send submission deadline reminder", [
                        'project_id' => $project->id,
                        'student_email' => $member->student->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    private static function sendDefensePreparationReminders(): void
    {
        // Find defenses in next 2 weeks
        $upcomingDefenses = Defense::where('status', 'scheduled')
            ->whereBetween('defense_date', [now(), now()->addWeeks(2)])
            ->with('project.team.members.student')
            ->get();

        foreach ($upcomingDefenses as $defense) {
            foreach ($defense->project->team->members as $member) {
                $data = [
                    'defense' => $defense,
                    'student' => $member->student,
                ];

                try {
                    self::sendEmail(
                        $member->student->email,
                        "Defense Preparation Reminder",
                        'emails.defense-preparation-reminder',
                        $data
                    );
                } catch (\Exception $e) {
                    Log::error("Failed to send defense preparation reminder", [
                        'defense_id' => $defense->id,
                        'student_email' => $member->student->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    private static function sendValidationDeadlineReminders(): void
    {
        // Find subjects pending validation for more than 1 week
        $overdueSubjects = Subject::pendingValidation()
            ->where('created_at', '<=', now()->subWeek())
            ->with('teacher')
            ->get();

        // Group by department
        $subjectsByDepartment = $overdueSubjects->groupBy('teacher.department');

        foreach ($subjectsByDepartment as $department => $subjects) {
            $departmentHead = User::where('role', 'department_head')
                ->where('department', $department)
                ->first();

            if ($departmentHead) {
                $data = [
                    'department_head' => $departmentHead,
                    'subjects' => $subjects,
                    'count' => $subjects->count(),
                ];

                try {
                    self::sendEmail(
                        $departmentHead->email,
                        "Overdue Subject Validations",
                        'emails.validation-deadline-reminder',
                        $data
                    );
                } catch (\Exception $e) {
                    Log::error("Failed to send validation deadline reminder", [
                        'department_head_email' => $departmentHead->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}