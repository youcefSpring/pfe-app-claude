<?php

namespace App\Services;

use App\Models\Defense;
use App\Models\DefenseReport;
use App\Models\User;
use App\Models\Project;
use App\Models\Subject;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PDF;

class ReportService
{
    /**
     * Generate defense report (PV de soutenance).
     */
    public function generateDefenseReport(Defense $defense, User $generator): DefenseReport
    {
        // Validate defense is completed
        if ($defense->status !== 'completed') {
            throw new \Exception('Defense must be completed to generate report');
        }

        // Check if report already exists
        if ($defense->report) {
            throw new \Exception('Report already exists for this defense');
        }

        // Generate report content
        $reportData = $this->prepareDefenseReportData($defense);

        // Generate PDF (using DomPDF or similar)
        $pdfContent = $this->generateDefenseReportPDF($reportData);

        // Save to storage
        $fileName = $this->generateReportFileName($defense);
        $filePath = "reports/defenses/{$fileName}";

        Storage::put($filePath, $pdfContent);

        // Create report record
        return DefenseReport::create([
            'defense_id' => $defense->id,
            'file_path' => $filePath,
            'generated_at' => now(),
            'generated_by' => $generator->id,
        ]);
    }

    /**
     * Generate statistical reports.
     */
    public function generateStatisticalReport(array $parameters): array
    {
        $startDate = Carbon::parse($parameters['start_date'] ?? now()->subYear());
        $endDate = Carbon::parse($parameters['end_date'] ?? now());
        $reportType = $parameters['type'] ?? 'summary';

        switch ($reportType) {
            case 'subject_analysis':
                return $this->generateSubjectAnalysisReport($startDate, $endDate);

            case 'team_performance':
                return $this->generateTeamPerformanceReport($startDate, $endDate);

            case 'supervisor_workload':
                return $this->generateSupervisorWorkloadReport($startDate, $endDate);

            case 'defense_statistics':
                return $this->generateDefenseStatisticsReport($startDate, $endDate);

            default:
                return $this->generateSummaryReport($startDate, $endDate);
        }
    }

    /**
     * Prepare data for defense report.
     */
    private function prepareDefenseReportData(Defense $defense): array
    {
        $project = $defense->project;
        $team = $project->team;
        $subject = $project->subject;

        return [
            'defense' => [
                'date' => $defense->defense_date ? \Carbon\Carbon::parse($defense->defense_date)->format('d/m/Y') : '',
                'time' => $defense->defense_time ? \Carbon\Carbon::parse($defense->defense_time)->format('H:i') : '',
                'room' => $defense->room->name,
                'duration' => $defense->duration,
                'final_grade' => $defense->final_grade,
                'notes' => $defense->notes,
            ],
            'project' => [
                'title' => $project->getTitle(),
                'description' => $project->getDescription(),
                'type' => $project->type,
                'start_date' => $project->started_at?->format('d/m/Y'),
                'submission_date' => $project->submitted_at?->format('d/m/Y'),
            ],
            'team' => [
                'name' => $team->name,
                'members' => $team->members()->with('student')->get()->map(function ($member) {
                    return [
                        'name' => $member->student->name,
                        'matricule' => $member->student->matricule,
                        'role' => $member->role,
                    ];
                })->toArray(),
            ],
            'subject' => $subject ? [
                'title' => $subject->title,
                'teacher' => $subject->teacher->name,
                'keywords' => $subject->keywords,
            ] : null,
            'external_project' => $project->externalProject ? [
                'company' => $project->externalProject->company,
                'contact_person' => $project->externalProject->contact_person,
                'description' => $project->externalProject->project_description,
            ] : null,
            'jury' => $defense->jury()->with('teacher')->get()->map(function ($juryMember) {
                return [
                    'name' => $juryMember->teacher->name,
                    'role' => $juryMember->role,
                    'grade' => $juryMember->individual_grade,
                    'comments' => $juryMember->comments,
                ];
            })->toArray(),
            'supervisor' => [
                'name' => $project->supervisor->name,
                'department' => $project->supervisor->department,
            ],
            'co_supervisor' => $project->coSupervisor ? [
                'name' => $project->coSupervisor->name,
                'department' => $project->coSupervisor->department,
            ] : null,
            'generated_at' => now()->format('d/m/Y H:i'),
        ];
    }

    /**
     * Generate PDF content for defense report.
     */
    private function generateDefenseReportPDF(array $data): string
    {
        $pdf = \PDF::loadView('reports.defense-pv', $data);
        return $pdf->output();
    }

    /**
     * Generate individual student defense report.
     */
    public function generateStudentDefenseReport(Defense $defense, User $student): string
    {
        $project = $defense->project;
        $team = $project->team;

        $data = $this->prepareStudentReportData($defense, $student);

        $pdf = \PDF::loadView('reports.defense-pv', $data);
        return $pdf->output();
    }

    /**
     * Generate batch reports for all students in a team.
     */
    public function generateBatchStudentReports(Defense $defense): array
    {
        $project = $defense->project;
        $team = $project->team;
        $reports = [];

        foreach ($team->members as $member) {
            $student = $member->student;
            $pdfContent = $this->generateStudentDefenseReport($defense, $student);
            $fileName = $this->generateStudentReportFileName($defense, $student);

            $reports[] = [
                'student_name' => $student->name,
                'filename' => $fileName,
                'content' => $pdfContent
            ];
        }

        return $reports;
    }

    /**
     * Prepare data for individual student report.
     */
    private function prepareStudentReportData(Defense $defense, User $student): array
    {
        $project = $defense->project;
        $team = $project->team;
        $subject = $project->subject;

        return [
            'defense' => [
                'date' => $defense->defense_date ? \Carbon\Carbon::parse($defense->defense_date)->format('d/m/Y') : '',
                'time' => $defense->defense_time ? \Carbon\Carbon::parse($defense->defense_time)->format('H:i') : '',
                'room' => $defense->room->name ?? '',
                'duration' => $defense->duration ?? '',
                'final_grade' => $defense->final_grade ?? '',
                'manuscript_grade' => $defense->manuscript_grade ?? '',
                'oral_grade' => $defense->oral_grade ?? '',
                'questions_grade' => $defense->questions_grade ?? '',
                'realization_grade' => $defense->realization_grade ?? '',
                'academic_year' => $project->academic_year ?? '2023/2024',
            ],
            'project' => [
                'title' => $project->getTitle() ?? $subject->title ?? '',
                'description' => $project->getDescription() ?? '',
                'type' => $project->type ?? '',
            ],
            'team' => [
                'name' => $team->name ?? '',
                'members' => [
                    [
                        'name' => $student->name ?? '',
                        'matricule' => $student->matricule ?? '',
                        'birth_date' => $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d/m/Y') : '',
                        'birth_place' => $student->birth_place ?? '',
                        'speciality' => $student->speciality ?? '',
                    ]
                ],
            ],
            'subject' => $subject ? [
                'title' => $subject->title ?? '',
                'teacher' => $subject->teacher->name ?? '',
            ] : null,
            'jury' => $defense->jury()->with('teacher')->get()->map(function ($juryMember) {
                return [
                    'name' => $juryMember->teacher->name ?? '',
                    'role' => ucfirst($juryMember->role ?? ''),
                    'grade' => $juryMember->teacher->grade ?? '',
                ];
            })->toArray(),
            'supervisor' => [
                'name' => $project->supervisor->name ?? '',
                'department' => $project->supervisor->department ?? '',
            ],
            'logo_path' => public_path('logo.png'),
        ];
    }

    /**
     * Generate report file name for individual student.
     */
    private function generateStudentReportFileName(Defense $defense, User $student): string
    {
        $date = $defense->defense_date ? \Carbon\Carbon::parse($defense->defense_date)->format('Y-m-d') : date('Y-m-d');
        $studentName = str_replace(' ', '_', $student->name);

        return "PV_Soutenance_{$studentName}_{$date}.pdf";
    }

    /**
     * Generate report file name.
     */
    private function generateReportFileName(Defense $defense): string
    {
        $project = $defense->project;
        $date = $defense->defense_date ? \Carbon\Carbon::parse($defense->defense_date)->format('Y-m-d') : date('Y-m-d');
        $teamName = str_replace(' ', '_', $project->team->name);

        return "PV_Soutenance_{$teamName}_{$date}.pdf";
    }

    /**
     * Generate subject analysis report.
     */
    private function generateSubjectAnalysisReport(Carbon $startDate, Carbon $endDate): array
    {
        $subjects = Subject::whereBetween('created_at', [$startDate, $endDate])
            ->with('teacher')
            ->get();

        $analysis = [
            'total_subjects' => $subjects->count(),
            'by_status' => $subjects->groupBy('status')->map->count(),
            'by_department' => $subjects->groupBy('teacher.department')->map->count(),
            'validation_time' => $this->calculateAverageValidationTime($subjects),
            'most_active_teachers' => $this->getMostActiveTeachers($subjects),
            'subject_selection_rate' => $this->calculateSubjectSelectionRate($subjects),
        ];

        return $analysis;
    }

    /**
     * Generate team performance report.
     */
    private function generateTeamPerformanceReport(Carbon $startDate, Carbon $endDate): array
    {
        $teams = Team::whereBetween('created_at', [$startDate, $endDate])
            ->with(['members.student', 'project.defense'])
            ->get();

        return [
            'total_teams' => $teams->count(),
            'completion_rate' => $this->calculateTeamCompletionRate($teams),
            'average_team_size' => $teams->avg(function ($team) {
                return $team->members->count();
            }),
            'formation_time' => $this->calculateAverageFormationTime($teams),
            'success_rate' => $this->calculateTeamSuccessRate($teams),
            'grade_distribution' => $this->getGradeDistribution($teams),
        ];
    }

    /**
     * Generate supervisor workload report.
     */
    private function generateSupervisorWorkloadReport(Carbon $startDate, Carbon $endDate): array
    {
        $supervisors = User::where('role', 'teacher')
            ->withCount(['supervisedProjects' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->with(['supervisedProjects' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get();

        return [
            'total_supervisors' => $supervisors->count(),
            'workload_distribution' => $supervisors->map(function ($supervisor) {
                return [
                    'name' => $supervisor->name,
                    'department' => $supervisor->department,
                    'project_count' => $supervisor->supervised_projects_count,
                    'current_workload' => $supervisor->getCurrentWorkload(),
                    'can_supervise_more' => $supervisor->canSuperviseMoreProjects(),
                ];
            }),
            'average_projects_per_supervisor' => $supervisors->avg('supervised_projects_count'),
            'workload_balance' => $this->calculateWorkloadBalance($supervisors),
        ];
    }

    /**
     * Generate defense statistics report.
     */
    private function generateDefenseStatisticsReport(Carbon $startDate, Carbon $endDate): array
    {
        $defenses = Defense::whereBetween('defense_date', [$startDate, $endDate])
            ->with('project.team')
            ->get();

        return [
            'total_defenses' => $defenses->count(),
            'by_status' => $defenses->groupBy('status')->map->count(),
            'average_grade' => $defenses->whereNotNull('final_grade')->avg('final_grade'),
            'grade_distribution' => $this->getDefenseGradeDistribution($defenses),
            'success_rate' => $this->calculateDefenseSuccessRate($defenses),
            'room_utilization' => $this->calculateRoomUtilization($defenses),
            'scheduling_efficiency' => $this->calculateSchedulingEfficiency($defenses),
        ];
    }

    /**
     * Generate summary report.
     */
    private function generateSummaryReport(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'period' => [
                'start' => $startDate->format('d/m/Y'),
                'end' => $endDate->format('d/m/Y'),
            ],
            'subjects' => [
                'total' => Subject::whereBetween('created_at', [$startDate, $endDate])->count(),
                'validated' => Subject::whereBetween('validated_at', [$startDate, $endDate])->count(),
            ],
            'teams' => [
                'total' => Team::whereBetween('created_at', [$startDate, $endDate])->count(),
                'complete' => Team::complete()->whereBetween('created_at', [$startDate, $endDate])->count(),
            ],
            'projects' => [
                'total' => Project::whereBetween('created_at', [$startDate, $endDate])->count(),
                'defended' => Project::where('status', 'defended')->whereBetween('created_at', [$startDate, $endDate])->count(),
            ],
            'defenses' => [
                'total' => Defense::whereBetween('defense_date', [$startDate, $endDate])->count(),
                'completed' => Defense::where('status', 'completed')->whereBetween('defense_date', [$startDate, $endDate])->count(),
            ],
        ];
    }

    /**
     * Helper methods for calculations.
     */
    private function calculateAverageValidationTime($subjects): float
    {
        $validatedSubjects = $subjects->whereNotNull('validated_at');

        if ($validatedSubjects->isEmpty()) {
            return 0;
        }

        $totalHours = $validatedSubjects->sum(function ($subject) {
            return $subject->created_at->diffInHours($subject->validated_at);
        });

        return round($totalHours / $validatedSubjects->count(), 2);
    }

    private function getMostActiveTeachers($subjects): array
    {
        return $subjects->groupBy('teacher_id')
            ->map(function ($group) {
                $teacher = $group->first()->teacher;
                return [
                    'name' => $teacher->name,
                    'department' => $teacher->department,
                    'subject_count' => $group->count(),
                ];
            })
            ->sortByDesc('subject_count')
            ->take(10)
            ->values()
            ->toArray();
    }

    private function calculateSubjectSelectionRate($subjects): float
    {
        $totalSubjects = $subjects->where('status', 'validated')->count();
        $selectedSubjects = $subjects->whereNotNull('teams')->count();

        return $totalSubjects > 0 ? round(($selectedSubjects / $totalSubjects) * 100, 2) : 0;
    }

    private function calculateTeamCompletionRate($teams): float
    {
        $totalTeams = $teams->count();
        $completedTeams = $teams->where('status', '!=', 'forming')->count();

        return $totalTeams > 0 ? round(($completedTeams / $totalTeams) * 100, 2) : 0;
    }

    private function calculateAverageFormationTime($teams): float
    {
        $completedTeams = $teams->where('status', '!=', 'forming');

        if ($completedTeams->isEmpty()) {
            return 0;
        }

        $totalHours = $completedTeams->sum(function ($team) {
            return $team->created_at->diffInHours($team->updated_at);
        });

        return round($totalHours / $completedTeams->count(), 2);
    }

    private function calculateTeamSuccessRate($teams): float
    {
        $totalTeams = $teams->count();
        $successfulTeams = $teams->filter(function ($team) {
            return $team->project && $team->project->defense && $team->project->defense->final_grade >= 10;
        })->count();

        return $totalTeams > 0 ? round(($successfulTeams / $totalTeams) * 100, 2) : 0;
    }

    private function getGradeDistribution($teams): array
    {
        $grades = $teams->map(function ($team) {
            return $team->project?->defense?->final_grade;
        })->filter()->toArray();

        return [
            'excellent' => count(array_filter($grades, fn($g) => $g >= 16)),
            'good' => count(array_filter($grades, fn($g) => $g >= 14 && $g < 16)),
            'satisfactory' => count(array_filter($grades, fn($g) => $g >= 12 && $g < 14)),
            'passing' => count(array_filter($grades, fn($g) => $g >= 10 && $g < 12)),
            'failing' => count(array_filter($grades, fn($g) => $g < 10)),
        ];
    }

    private function calculateWorkloadBalance($supervisors): float
    {
        $workloads = $supervisors->pluck('supervised_projects_count');
        $mean = $workloads->avg();
        $variance = $workloads->map(function ($workload) use ($mean) {
            return pow($workload - $mean, 2);
        })->avg();

        return round(sqrt($variance), 2); // Standard deviation
    }

    private function getDefenseGradeDistribution($defenses): array
    {
        $grades = $defenses->whereNotNull('final_grade')->pluck('final_grade')->toArray();

        return [
            'excellent' => count(array_filter($grades, fn($g) => $g >= 16)),
            'good' => count(array_filter($grades, fn($g) => $g >= 14 && $g < 16)),
            'satisfactory' => count(array_filter($grades, fn($g) => $g >= 12 && $g < 14)),
            'passing' => count(array_filter($grades, fn($g) => $g >= 10 && $g < 12)),
            'failing' => count(array_filter($grades, fn($g) => $g < 10)),
        ];
    }

    private function calculateDefenseSuccessRate($defenses): float
    {
        $totalDefenses = $defenses->whereNotNull('final_grade')->count();
        $successfulDefenses = $defenses->where('final_grade', '>=', 10)->count();

        return $totalDefenses > 0 ? round(($successfulDefenses / $totalDefenses) * 100, 2) : 0;
    }

    private function calculateRoomUtilization($defenses): array
    {
        return $defenses->groupBy('room_id')
            ->map(function ($group) {
                $room = $group->first()->room;
                return [
                    'room_name' => $room->name,
                    'usage_count' => $group->count(),
                    'total_hours' => $group->sum('duration') / 60,
                ];
            })
            ->sortByDesc('usage_count')
            ->values()
            ->toArray();
    }

    private function calculateSchedulingEfficiency($defenses): float
    {
        // Calculate efficiency based on time slot utilization
        $totalSlots = $defenses->count();
        $optimalSlots = $defenses->groupBy('defense_date')->count() * 8; // 8 slots per day

        return $optimalSlots > 0 ? round(($totalSlots / $optimalSlots) * 100, 2) : 0;
    }
}