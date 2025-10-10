<?php

namespace App\Http\Middleware;

use App\Models\AcademicYear;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CurrentAcademicYearData
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        $currentYear = AcademicYear::getCurrentYear();

        if (!$currentYear) {
            return redirect()->route('dashboard')
                ->with('error', __('app.no_current_academic_year'));
        }

        // Store current academic year in the request for use by controllers
        $request->merge(['current_academic_year' => $currentYear->year]);

        // Apply academic year filtering globally for non-admin users
        $this->applyAcademicYearFilter($currentYear->year);

        return $next($request);
    }

    /**
     * Apply academic year filtering to models
     */
    private function applyAcademicYearFilter(string $academicYear): void
    {
        // Apply global scopes to filter by current academic year
        \App\Models\Subject::addGlobalScope('academic_year', function ($builder) use ($academicYear) {
            $builder->where('academic_year', $academicYear);
        });

        \App\Models\Team::addGlobalScope('academic_year', function ($builder) use ($academicYear) {
            $builder->where('academic_year', $academicYear);
        });

        \App\Models\Project::addGlobalScope('academic_year', function ($builder) use ($academicYear) {
            $builder->where('academic_year', $academicYear);
        });

        \App\Models\Defense::addGlobalScope('academic_year', function ($builder) use ($academicYear) {
            $builder->where('academic_year', $academicYear);
        });
    }
}