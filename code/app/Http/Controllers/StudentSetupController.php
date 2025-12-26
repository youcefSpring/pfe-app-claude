<?php

namespace App\Http\Controllers;

use App\Models\StudentMark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StudentSetupController extends Controller
{
    /**
     * Show streamlined setup page.
     */
    public function streamlined(): View
    {
        $user = Auth::user();
        return view('student.setup.streamlined', compact('user'));
    }

    /**
     * Show welcome page for first-time setup.
     */
    public function welcome(): View
    {
        $user = Auth::user();
        return view('student.setup.welcome', compact('user'));
    }

    /**
     * Show personal information form.
     */
    public function personalInfo(): View
    {
        $user = Auth::user();
        return view('student.setup.personal-info', compact('user'));
    }

    /**
     * Store personal information.
     */
    public function storePersonalInfo(Request $request): RedirectResponse
    {
        // CHECK SETTINGS: Birth certificate requirement
        $birthCertificateRequired = \App\Services\SettingsService::requiresBirthCertificate();

        $rules = [
            'date_naissance' => 'required|date|before:today',
            'lieu_naissance' => 'required|string|max:255',
            'student_level' => 'required|in:licence_3,master_1,master_2',
        ];

        // Add birth certificate rule if required by settings
        if ($birthCertificateRequired) {
            $rules['birth_certificate'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
        } else {
            $rules['birth_certificate'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();

        // Handle birth certificate upload
        if ($request->hasFile('birth_certificate')) {
            $file = $request->file('birth_certificate');
            $fileName = 'birth_certificates/' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public', $fileName);
            $publicPath = str_replace('public/', '', $path);
        }

        // Update user information
        $user->update([
            'date_naissance' => $request->date_naissance,
            'lieu_naissance' => $request->lieu_naissance,
            'student_level' => $request->student_level,
            'birth_certificate_path' => $publicPath ?? null,
            'birth_certificate_status' => 'pending',
        ]);

        return redirect()->route('student.setup.marks');
    }

    /**
     * Show marks entry form.
     */
    public function marks(): View
    {
        $user = Auth::user();
        $requiredMarks = $user->getRequiredPreviousMarks();

        // Get existing marks for the student
        $existingMarks = StudentMark::where('user_id', $user->id)
            ->where('academic_year', '<', now()->year)
            ->get();

        return view('student.setup.marks', compact('user', 'requiredMarks', 'existingMarks'));
    }

    /**
     * Store marks information.
     */
    public function storeMarks(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // CHECK SETTINGS: Previous marks requirement
        $marksRequired = \App\Services\SettingsService::requiresPreviousMarks();

        if (!$marksRequired) {
            // If marks not required, skip this step and complete setup
            $user->update(['profile_completed' => true]);
            return redirect()->route('student.setup.complete');
        }

        $requiredMarks = $user->getRequiredPreviousMarks();

        // Build validation rules based on required marks
        $rules = [];
        for ($i = 1; $i <= $requiredMarks; $i++) {
            $rules["semester_{$i}_mark"] = 'required|numeric|min:0|max:20';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Clear existing marks for previous years
        StudentMark::where('user_id', $user->id)
            ->where('academic_year', '<', now()->year)
            ->delete();

        // Store new marks (previous year marks)
        for ($i = 1; $i <= $requiredMarks; $i++) {
            $markName = "Mark {$i}";
            if ($user->student_level === 'licence_3') {
                $markName = "Mark {$i}";
            } else {
                $markName = "Mark {$i} - Previous Year";
            }

            StudentMark::create([
                'user_id' => $user->id,
                'subject_name' => $markName,
                'mark' => $request->input("semester_{$i}_mark"),
                'academic_year' => \App\Services\PfeHelper::getAcademicYear(now()->subYear()), // Previous academic year
                'created_by' => $user->id,
            ]);
        }

        return redirect()->route('student.setup.complete');
    }

    /**
     * Show completion page and mark profile as complete.
     */
    public function complete(): View
    {
        $user = Auth::user();

        // Mark profile as complete
        $user->update(['profile_completed' => true]);

        return view('student.setup.complete', compact('user'));
    }

    /**
     * Finish setup and redirect to dashboard.
     */
    public function finish(): RedirectResponse
    {
        return redirect()->route('dashboard')->with('success', __('app.profile_setup_completed'));
    }
}
