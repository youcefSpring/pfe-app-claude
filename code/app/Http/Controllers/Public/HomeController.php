<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
// Temporarily disabled - tables not created yet
// use App\Models\BlogPost;
// use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index(): View
    {
        // Get the main teacher/owner user
        $teacher = User::where('role', 'teacher')->first();

        // Temporarily use PFE data instead of blog/project data
        $pfeProjectModel = \App\Models\PfeProject::class;
        $subjectModel = \App\Models\Subject::class;

        // Get featured PFE projects instead of public projects
        $featuredProjects = $pfeProjectModel::where('status', 'in_progress')
            ->latest()
            ->limit(3)
            ->get();

        // Get latest published subjects instead of blog posts
        $latestPosts = $subjectModel::where('status', 'published')
            ->latest()
            ->limit(3)
            ->get();

        return view('public.home', compact(
            'teacher',
            'featuredProjects',
            'latestPosts'
        ));
    }

    /**
     * Display the about page.
     */
    public function about(): View
    {
        $teacher = User::where('role', 'teacher')
            ->with(['credentials' => function ($query) {
                $query->valid()->latest();
            }])
            ->first();

        return view('public.about', compact('teacher'));
    }

    /**
     * Download CV file.
     */
    public function downloadCV()
    {
        $teacher = User::where('role', 'teacher')->first();

        if (!$teacher || !$teacher->cv_file_path || !file_exists(storage_path('app/' . $teacher->cv_file_path))) {
            abort(404, 'CV not found.');
        }

        $filePath = storage_path('app/' . $teacher->cv_file_path);
        $fileName = $teacher->name . '_CV.pdf';

        return response()->download($filePath, $fileName);
    }
}