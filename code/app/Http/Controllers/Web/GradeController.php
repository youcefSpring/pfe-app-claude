<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\StudentGrade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    /**
     * Display student grades dashboard
     */
    public function index(): View
    {
        $user = Auth::user();

        if ($user->isStudent()) {
            $grades = $user->grades()
                ->orderBy('academic_year', 'desc')
                ->orderBy('semester', 'desc')
                ->paginate(15);
        } else {
            // For admin/department head - show grades needing verification
            $grades = StudentGrade::with(['student', 'verifiedBy'])
                ->pendingVerification()
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('grades.index', compact('grades'));
    }

    /**
     * Show form for adding new grade
     */
    public function create(): View
    {
        return view('grades.create');
    }

    /**
     * Store new grade
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject_name' => 'required|string|max:255',
            'semester' => 'required|string|max:10',
            'academic_year' => 'required|string|max:20',
            'grade' => 'required|numeric|min:0|max:20',
            'coefficient' => 'required|numeric|min:0.1|max:10',
        ]);

        $validated['student_id'] = Auth::id();
        $validated['status'] = 'draft';

        StudentGrade::create($validated);

        return redirect()->route('grades.index')
            ->with('success', 'Grade added successfully! Submit it for verification when ready.');
    }

    /**
     * Show edit grade form
     */
    public function edit(StudentGrade $grade): View
    {
        $this->authorize('update', $grade);

        return view('grades.edit', compact('grade'));
    }

    /**
     * Update grade
     */
    public function update(Request $request, StudentGrade $grade): RedirectResponse
    {
        $this->authorize('update', $grade);

        $validated = $request->validate([
            'subject_name' => 'required|string|max:255',
            'semester' => 'required|string|max:10',
            'academic_year' => 'required|string|max:20',
            'grade' => 'required|numeric|min:0|max:20',
            'coefficient' => 'required|numeric|min:0.1|max:10',
        ]);

        $grade->update($validated);

        return redirect()->route('grades.index')
            ->with('success', 'Grade updated successfully!');
    }

    /**
     * Submit grade for verification
     */
    public function submitForVerification(StudentGrade $grade): RedirectResponse
    {
        $this->authorize('update', $grade);

        if ($grade->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft grades can be submitted for verification.');
        }

        $grade->update([
            'status' => 'pending_verification',
            'submitted_at' => now(),
        ]);

        return redirect()->route('grades.index')
            ->with('success', 'Grade submitted for verification!');
    }

    /**
     * Delete grade
     */
    public function destroy(StudentGrade $grade): RedirectResponse
    {
        $this->authorize('delete', $grade);

        if ($grade->status === 'verified') {
            return redirect()->back()
                ->with('error', 'Cannot delete verified grades.');
        }

        $grade->delete();

        return redirect()->route('grades.index')
            ->with('success', 'Grade deleted successfully!');
    }

    /**
     * Show pending grades for verification
     */
    public function pendingVerification(): View
    {
        $grades = StudentGrade::with(['student'])
            ->pendingVerification()
            ->orderBy('submitted_at', 'asc')
            ->paginate(20);

        return view('grades.pending', compact('grades'));
    }

    /**
     * Verify a grade
     */
    public function verify(Request $request, StudentGrade $grade): RedirectResponse
    {
        $validated = $request->validate([
            'verification_notes' => 'nullable|string|max:500',
        ]);

        $grade->update([
            'status' => 'verified',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'verification_notes' => $validated['verification_notes'] ?? null,
        ]);

        return redirect()->back()
            ->with('success', 'Grade verified successfully!');
    }

    /**
     * Reject a grade
     */
    public function reject(Request $request, StudentGrade $grade): RedirectResponse
    {
        $validated = $request->validate([
            'verification_notes' => 'required|string|max:500',
        ]);

        $grade->update([
            'status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'verification_notes' => $validated['verification_notes'],
        ]);

        return redirect()->back()
            ->with('success', 'Grade rejected with feedback.');
    }

    /**
     * Batch verify grades
     */
    public function batchVerify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'grade_ids' => 'required|array',
            'grade_ids.*' => 'exists:student_grades,id',
            'verification_notes' => 'nullable|string|max:500',
        ]);

        $gradeIds = $validated['grade_ids'];
        $notes = $validated['verification_notes'] ?? null;

        StudentGrade::whereIn('id', $gradeIds)
            ->where('status', 'pending_verification')
            ->update([
                'status' => 'verified',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'verification_notes' => $notes,
            ]);

        $count = count($gradeIds);
        return redirect()->back()
            ->with('success', "Successfully verified {$count} grades!");
    }

    /**
     * Show grade details
     */
    public function show(StudentGrade $grade): View
    {
        $grade->load(['student', 'verifiedBy']);

        return view('grades.show', compact('grade'));
    }

    /**
     * Calculate student average
     */
    public function calculateAverage(User $student): float
    {
        $verifiedGrades = $student->grades()->verified()->get();

        if ($verifiedGrades->isEmpty()) {
            return 0.0;
        }

        $totalWeightedGrades = $verifiedGrades->sum(function ($grade) {
            return $grade->getWeightedGrade();
        });

        $totalCoefficients = $verifiedGrades->sum('coefficient');

        return round($totalWeightedGrades / $totalCoefficients, 2);
    }
}
