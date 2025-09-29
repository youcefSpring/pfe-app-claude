<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// Blog functionality disabled - tables not created yet
// use App\Models\BlogPost;
// use App\Models\ContactMessage;
// use App\Models\Course;
// use App\Models\Project;
// use App\Models\Publication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the admin dashboard.
     */
    public function index(): View
    {
        // Import PFE models at method level to avoid class-level dependencies
        $pfeProjectModel = \App\Models\PfeProject::class;
        $subjectModel = \App\Models\Subject::class;
        $teamModel = \App\Models\Team::class;
        $userModel = \App\Models\User::class;

        // Basic PFE statistics
        $stats = [
            'total_users' => $userModel::count(),
            'students' => $userModel::where('role', 'student')->count(),
            'teachers' => $userModel::where('role', 'teacher')->count(),
            'subjects' => $subjectModel::count(),
            'teams' => $teamModel::count(),
            'projects' => $pfeProjectModel::count(),
        ];

        // Recent activities (using existing PFE data)
        $recentUsers = $userModel::latest()->limit(5)->get();
        $recentSubjects = $subjectModel::latest()->limit(5)->get();
        $recentTeams = $teamModel::latest()->limit(5)->get();
        $recentProjects = $pfeProjectModel::latest()->limit(5)->get();

        // Content status breakdown
        $contentStatus = [
            'published_subjects' => $subjectModel::where('status', 'published')->count(),
            'pending_subjects' => $subjectModel::where('status', 'submitted')->count(),
            'validated_teams' => $teamModel::where('status', 'validated')->count(),
            'active_projects' => $pfeProjectModel::where('status', 'in_progress')->count(),
        ];

        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
            'recentSubjects',
            'recentTeams',
            'recentProjects',
            'contentStatus'
        ));
    }
}