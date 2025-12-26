<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SettingsService;

class CheckStudentSubjectCreation
{
    /**
     * Handle an incoming request.
     *
     * Check if students can create subjects in system settings before allowing access.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!SettingsService::canStudentsCreateSubjects()) {
            return redirect()
                ->route('dashboard')
                ->with('error', __('app.student_subject_creation_disabled'));
        }

        return $next($request);
    }
}
