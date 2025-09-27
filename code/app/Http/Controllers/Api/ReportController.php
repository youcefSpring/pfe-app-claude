<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function __construct(private ReportingService $reportingService)
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Generate defense schedule report
     */
    public function defenseSchedule(Request $request): JsonResponse|Response
    {
        $this->authorize('viewReports', ReportController::class);

        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'format' => 'nullable|in:json,pdf,excel',
            'department' => 'nullable|string|in:informatique,mathematiques,physique',
            'status' => 'nullable|string|in:scheduled,confirmed,completed'
        ]);

        $filters = $request->only(['date_from', 'date_to', 'department', 'status']);
        $format = $request->get('format', 'json');

        $report = $this->reportingService->generateDefenseReport($filters);

        if ($format === 'json') {
            return response()->json([
                'report' => $report,
                'filters' => $filters,
                'generated_at' => now()
            ]);
        }

        // For PDF/Excel, export and return file URL
        $filePath = $this->reportingService->exportToExcel('defense_schedule', $report, $filters);

        return response()->json([
            'report_url' => url("api/v1/files/" . base64_encode($filePath)),
            'format' => $format,
            'generated_at' => now()
        ]);
    }

    /**
     * Generate project progress report
     */
    public function projectProgress(Request $request): JsonResponse
    {
        $this->authorize('viewReports', ReportController::class);

        $request->validate([
            'supervisor_id' => 'nullable|integer|exists:users,id',
            'status' => 'nullable|string|in:assigned,in_progress,under_review,ready_for_defense,defended,completed',
            'department' => 'nullable|string|in:informatique,mathematiques,physique',
            'overdue_only' => 'nullable|boolean'
        ]);

        $filters = $request->only(['supervisor_id', 'status', 'department']);

        $report = $this->reportingService->generateProgressReport($filters);

        // Filter overdue projects if requested
        if ($request->boolean('overdue_only')) {
            $report['projects'] = $report['projects']->filter(function ($project) {
                return $project['is_overdue'];
            })->values();
        }

        return response()->json([
            'projects' => $report['projects'],
            'statistics' => [
                'total_projects' => $report['total_projects'],
                'by_status' => $report['by_status'],
                'projects_with_deliverables' => $report['projects_with_deliverables'],
                'overdue_projects' => $report['overdue_projects']
            ],
            'filters' => $filters,
            'generated_at' => now()
        ]);
    }

    /**
     * Generate team performance report
     */
    public function teamPerformance(Request $request): JsonResponse
    {
        $this->authorize('viewReports', ReportController::class);

        $request->validate([
            'year' => 'nullable|integer|min:2020|max:' . (now()->year + 1),
            'department' => 'nullable|string|in:informatique,mathematiques,physique'
        ]);

        $filters = $request->only(['year', 'department']);

        $report = $this->reportingService->generateTeamReport($filters);

        return response()->json([
            'teams' => $report['teams'],
            'metrics' => [
                'total_teams' => $report['total_teams'],
                'by_status' => $report['by_status'],
                'by_size' => $report['by_size'],
                'assigned_teams' => $report['assigned_teams'],
                'average_size' => $report['average_size']
            ],
            'filters' => $filters,
            'generated_at' => now()
        ]);
    }

    /**
     * Generate subject analysis report
     */
    public function subjectAnalysis(Request $request): JsonResponse
    {
        $this->authorize('viewReports', ReportController::class);

        $request->validate([
            'department' => 'nullable|string|in:informatique,mathematiques,physique',
            'status' => 'nullable|string|in:draft,submitted,approved,rejected,needs_correction,published',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from'
        ]);

        $filters = $request->only(['department', 'status', 'date_from', 'date_to']);

        $report = $this->reportingService->generateSubjectReport($filters);

        return response()->json([
            'subjects' => $report['subjects'],
            'statistics' => [
                'total_subjects' => $report['total_subjects'],
                'by_status' => $report['by_status'],
                'by_department' => $report['by_department'],
                'assigned_subjects' => $report['assigned_subjects']
            ],
            'filters' => $filters,
            'generated_at' => now()
        ]);
    }

    /**
     * Generate supervisor workload report
     */
    public function supervisorWorkload(Request $request): JsonResponse
    {
        $this->authorize('viewReports', ReportController::class);

        $request->validate([
            'department' => 'nullable|string|in:informatique,mathematiques,physique',
            'min_projects' => 'nullable|integer|min:0',
            'max_projects' => 'nullable|integer|min:0'
        ]);

        $report = $this->reportingService->generateSupervisorWorkloadReport();

        // Apply filters
        if ($request->has('department')) {
            $report['supervisors'] = $report['supervisors']->filter(function ($supervisor) use ($request) {
                return $supervisor['department'] === $request->department;
            })->values();
        }

        if ($request->has('min_projects')) {
            $report['supervisors'] = $report['supervisors']->filter(function ($supervisor) use ($request) {
                return $supervisor['projects_count'] >= $request->min_projects;
            })->values();
        }

        if ($request->has('max_projects')) {
            $report['supervisors'] = $report['supervisors']->filter(function ($supervisor) use ($request) {
                return $supervisor['projects_count'] <= $request->max_projects;
            })->values();
        }

        return response()->json([
            'supervisors' => $report['supervisors'],
            'statistics' => [
                'total_supervisors' => $report['total_supervisors'],
                'average_projects' => $report['average_projects'],
                'max_projects' => $report['max_projects'],
                'workload_distribution' => $report['workload_distribution']
            ],
            'filters' => $request->only(['department', 'min_projects', 'max_projects']),
            'generated_at' => now()
        ]);
    }

    /**
     * Generate comprehensive dashboard stats
     */
    public function dashboardStats(Request $request): JsonResponse
    {
        $user = $request->user();
        $stats = $this->reportingService->generateDashboardStats($user);

        return response()->json([
            'stats' => $stats,
            'user_role' => $user->getRoleNames()->first(),
            'generated_at' => now()
        ]);
    }

    /**
     * Export report to Excel/PDF
     */
    public function export(Request $request): JsonResponse
    {
        $this->authorize('exportReports', ReportController::class);

        $request->validate([
            'report_type' => 'required|string|in:subjects,teams,projects,defenses,supervisors',
            'format' => 'required|string|in:excel,pdf',
            'filters' => 'nullable|array'
        ]);

        $reportType = $request->report_type;
        $format = $request->format;
        $filters = $request->get('filters', []);

        // Generate the report data
        switch ($reportType) {
            case 'subjects':
                $data = $this->reportingService->generateSubjectReport($filters);
                break;
            case 'teams':
                $data = $this->reportingService->generateTeamReport($filters);
                break;
            case 'projects':
                $data = $this->reportingService->generateProgressReport($filters);
                break;
            case 'defenses':
                $data = $this->reportingService->generateDefenseReport($filters);
                break;
            case 'supervisors':
                $data = $this->reportingService->generateSupervisorWorkloadReport();
                break;
            default:
                return response()->json([
                    'error' => 'Invalid Report Type',
                    'message' => 'The specified report type is not supported'
                ], 422);
        }

        // Export the data
        $filePath = $this->reportingService->exportToExcel($reportType, $data, $filters);

        return response()->json([
            'download_url' => url("api/v1/files/" . base64_encode($filePath)),
            'file_name' => basename($filePath),
            'format' => $format,
            'report_type' => $reportType,
            'generated_at' => now()
        ]);
    }

    /**
     * Get available report types and their descriptions
     */
    public function reportTypes(): JsonResponse
    {
        return response()->json([
            'report_types' => [
                'defense_schedule' => [
                    'name' => 'Defense Schedule',
                    'description' => 'Comprehensive defense scheduling report with dates, rooms, and jury information',
                    'filters' => ['date_from', 'date_to', 'department', 'status'],
                    'formats' => ['json', 'pdf', 'excel']
                ],
                'project_progress' => [
                    'name' => 'Project Progress',
                    'description' => 'Track project advancement and deliverable submissions',
                    'filters' => ['supervisor_id', 'status', 'department', 'overdue_only'],
                    'formats' => ['json', 'excel']
                ],
                'team_performance' => [
                    'name' => 'Team Performance',
                    'description' => 'Analysis of team formation and project assignments',
                    'filters' => ['year', 'department'],
                    'formats' => ['json', 'excel']
                ],
                'subject_analysis' => [
                    'name' => 'Subject Analysis',
                    'description' => 'Overview of subject proposals and validation status',
                    'filters' => ['department', 'status', 'date_from', 'date_to'],
                    'formats' => ['json', 'excel']
                ],
                'supervisor_workload' => [
                    'name' => 'Supervisor Workload',
                    'description' => 'Distribution of projects among supervisors',
                    'filters' => ['department', 'min_projects', 'max_projects'],
                    'formats' => ['json', 'excel']
                ]
            ]
        ]);
    }
}