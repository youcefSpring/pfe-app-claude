<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin_pfe,chef_master,teacher');
    }

    public function index()
    {
        return view('pfe.reports.index');
    }

    public function defenseSchedule()
    {
        return view('pfe.reports.defense-schedule');
    }

    public function defenseStatistics()
    {
        return view('pfe.reports.defense-statistics');
    }

    public function projectProgress()
    {
        return view('pfe.reports.project-progress');
    }

    public function projectAssignments()
    {
        return view('pfe.reports.project-assignments');
    }

    public function teamPerformance()
    {
        return view('pfe.reports.team-performance');
    }

    public function teamStatistics()
    {
        return view('pfe.reports.team-statistics');
    }

    public function subjectAnalysis()
    {
        return view('pfe.reports.subject-analysis');
    }

    public function subjectDemand()
    {
        return view('pfe.reports.subject-demand');
    }

    public function systemUsage()
    {
        return view('pfe.reports.system-usage');
    }

    public function userActivity()
    {
        return view('pfe.reports.user-activity');
    }

    public function export(Request $request, string $report)
    {
        return redirect()->back()->with('success', 'Report export initiated.');
    }
}