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

        return view('pfe.dashboard.admin', [
            'user' => $user,
            'stats' => $stats
        ]);
    }
}