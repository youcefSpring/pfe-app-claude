<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ReportingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private ReportingService $reportingService)
    {
        $this->middleware('auth');
    }

    /**
     * Display the dashboard based on user role
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $stats = $this->reportingService->generateDashboardStats($user);

        return view('pfe.dashboard.index', [
            'user' => $user,
            'stats' => $stats,
            'role' => $user->role
        ]);
    }

    /**
     * Student dashboard
     */
    public function student(Request $request): View
    {
        $user = $request->user();
        $stats = $this->reportingService->generateDashboardStats($user);

        return view('pfe.dashboard.student', [
            'user' => $user,
            'stats' => $stats
        ]);
    }

    /**
     * Teacher dashboard
     */
    public function teacher(Request $request): View
    {
        $user = $request->user();
        $stats = $this->reportingService->generateDashboardStats($user);

        return view('pfe.dashboard.teacher', [
            'user' => $user,
            'stats' => $stats
        ]);
    }

    /**
     * Admin dashboard
     */
    public function admin(Request $request): View
    {
        $user = $request->user();
        $stats = $this->reportingService->generateDashboardStats($user);

        // Sample data for the new dashboard design
        $recentSubjects = [
            [
                'id' => 1,
                'title' => 'AI-powered E-commerce Recommendation System',
                'supervisor' => 'Dr. Ahmed Hassan',
                'status' => 'published',
                'domain' => 'AI & Machine Learning',
                'created_at' => now()->subDays(2)->diffForHumans(),
            ],
            [
                'id' => 2,
                'title' => 'Blockchain-based Voting System',
                'supervisor' => 'Prof. Sara Mohamed',
                'status' => 'approved',
                'domain' => 'Cybersecurity',
                'created_at' => now()->subDays(5)->diffForHumans(),
            ],
            [
                'id' => 3,
                'title' => 'Mobile Health Monitoring App',
                'supervisor' => 'Dr. Omar Ali',
                'status' => 'pending',
                'domain' => 'Mobile Development',
                'created_at' => now()->subWeek()->diffForHumans(),
            ],
        ];

        $activeTeams = [
            [
                'id' => 1,
                'name' => 'Tech Innovators',
                'member_count' => 3,
                'status' => 'validated',
                'project' => 'AI Recommendation System',
                'leader' => 'Alice Johnson',
                'created_at' => now()->subDays(10)->diffForHumans(),
            ],
            [
                'id' => 2,
                'name' => 'Code Warriors',
                'member_count' => 2,
                'status' => 'formed',
                'project' => 'Not Assigned',
                'leader' => 'Bob Smith',
                'created_at' => now()->subDays(15)->diffForHumans(),
            ],
            [
                'id' => 3,
                'name' => 'Digital Solutions',
                'member_count' => 3,
                'status' => 'forming',
                'project' => 'Not Assigned',
                'leader' => 'Carol Davis',
                'created_at' => now()->subDays(3)->diffForHumans(),
            ],
        ];

        $pendingActions = [
            [
                'task' => 'Subject Validations Pending',
                'priority' => 'high',
                'count' => 5,
                'url' => route('pfe.subjects.index') . '?status=pending',
            ],
            [
                'task' => 'Teams Without Projects',
                'priority' => 'medium',
                'count' => 8,
                'url' => route('pfe.teams.index') . '?status=unassigned',
            ],
            [
                'task' => 'Defense Scheduling Required',
                'priority' => 'high',
                'count' => 3,
                'url' => route('pfe.defenses.index') . '?action=schedule',
            ],
            [
                'task' => 'Overdue Deliverables',
                'priority' => 'medium',
                'count' => 12,
                'url' => route('pfe.projects.index') . '?filter=overdue',
            ],
        ];

        return view('pfe.dashboard.admin', [
            'user' => $user,
            'stats' => $stats,
            'recentSubjects' => $recentSubjects,
            'activeTeams' => $activeTeams,
            'pendingActions' => $pendingActions,
        ]);
    }
}