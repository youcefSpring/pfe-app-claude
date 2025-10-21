<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AllocationDeadline;
use App\Models\SubjectAllocation;
use App\Models\SubjectPreference;
use App\Models\User;
use App\Models\Subject;
use App\Http\Controllers\Web\GradeController;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AllocationController extends Controller
{
    protected $gradeController;

    public function __construct(GradeController $gradeController)
    {
        $this->gradeController = $gradeController;
    }

    public function index(): View
    {
        $allocations = SubjectAllocation::with(['student', 'subject', 'allocationDeadline'])
            ->orderBy('allocation_rank')
            ->paginate(20);

        $deadlines = AllocationDeadline::orderBy('created_at', 'desc')->get();

        return view('allocations.index', compact('allocations', 'deadlines'));
    }

    public function myAllocation(): View
    {
        $user = Auth::user();
        $allocation = $user->subjectAllocation;
        $preferences = $user->subjectPreferences()
            ->with('subject')
            ->orderBy('preference_order')
            ->get();

        $currentDeadline = AllocationDeadline::active()->first();

        return view('allocations.my-allocation', compact('allocation', 'preferences', 'currentDeadline'));
    }

    public function deadlines(): View
    {
        $deadlines = AllocationDeadline::orderBy('created_at', 'desc')->paginate(15);

        return view('allocations.deadlines', compact('deadlines'));
    }

    public function storeDeadline(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date|after:now',
            'academic_year' => 'required|string|max:20',
            'semester' => 'required|string|max:10',
        ]);

        AllocationDeadline::where('status', 'active')->update(['status' => 'inactive']);

        $validated['status'] = 'active';
        $validated['created_by'] = Auth::id();

        AllocationDeadline::create($validated);

        return redirect()->route('allocations.deadlines')
            ->with('success', 'Allocation deadline created successfully!');
    }

    public function updateDeadline(Request $request, AllocationDeadline $deadline): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'status' => 'required|in:active,inactive,completed',
        ]);

        if ($validated['status'] === 'active') {
            AllocationDeadline::where('id', '!=', $deadline->id)
                ->where('status', 'active')
                ->update(['status' => 'inactive']);
        }

        $deadline->update($validated);

        return redirect()->route('allocations.deadlines')
            ->with('success', 'Deadline updated successfully!');
    }

    public function runAllocation(Request $request): RedirectResponse
    {
        $currentDeadline = AllocationDeadline::active()->first();

        if (!$currentDeadline) {
            return redirect()->route('allocations.index')
                ->with('error', 'No active deadline found for allocation.');
        }

        if ($currentDeadline->preferences_deadline->isFuture()) {
            return redirect()->route('allocations.index')
                ->with('error', 'Cannot run allocation before deadline expires.');
        }

        try {
            DB::transaction(function () use ($currentDeadline) {
                $this->performAllocation($currentDeadline);
            });

            $currentDeadline->update(['status' => 'completed']);

            return redirect()->route('allocations.results')
                ->with('success', 'Subject allocation completed successfully!');
        } catch (\Exception $e) {
            return redirect()->route('allocations.index')
                ->with('error', 'Allocation failed: ' . $e->getMessage());
        }
    }

    public function results(): View
    {
        $allocations = SubjectAllocation::with(['student', 'subject', 'allocationDeadline'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total_allocations' => SubjectAllocation::count(),
            'confirmed_allocations' => SubjectAllocation::confirmed()->count(),
            'tentative_allocations' => SubjectAllocation::tentative()->count(),
            'first_choice_allocations' => SubjectAllocation::where('student_preference_order', 1)->count(),
        ];

        return view('allocations.results', compact('allocations', 'stats'));
    }

    public function confirm(SubjectAllocation $allocation): RedirectResponse
    {
        $allocation->confirm(Auth::user());

        return redirect()->back()
            ->with('success', 'Allocation confirmed successfully!');
    }

    public function reject(SubjectAllocation $allocation): RedirectResponse
    {
        $request = request();
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $allocation->reject(Auth::user(), $validated['rejection_reason']);

        return redirect()->back()
            ->with('success', 'Allocation rejected. Reason: ' . $validated['rejection_reason']);
    }

    protected function performAllocation(AllocationDeadline $deadline): void
    {
        SubjectAllocation::where('allocation_deadline_id', $deadline->id)->delete();

        $students = User::whereHas('subjectPreferences', function ($query) use ($deadline) {
            $query->where('allocation_deadline_id', $deadline->id)
                  ->where('status', 'submitted');
        })->get();

        $studentsWithGrades = [];
        foreach ($students as $student) {
            $average = $this->gradeController->calculateAverage($student);
            if ($average > 0) {
                $studentsWithGrades[] = [
                    'student' => $student,
                    'average' => $average,
                ];
            }
        }

        usort($studentsWithGrades, function ($a, $b) {
            return $b['average'] <=> $a['average'];
        });

        $allocatedSubjects = [];
        $allocationRank = 1;

        foreach ($studentsWithGrades as $studentData) {
            $student = $studentData['student'];
            $average = $studentData['average'];

            $preferences = $student->subjectPreferences()
                ->where('allocation_deadline_id', $deadline->id)
                ->where('status', 'submitted')
                ->orderBy('preference_order')
                ->get();

            $allocated = false;

            foreach ($preferences as $preference) {
                if (!in_array($preference->subject_id, $allocatedSubjects)) {
                    SubjectAllocation::create([
                        'allocation_deadline_id' => $deadline->id,
                        'student_id' => $student->id,
                        'subject_id' => $preference->subject_id,
                        'student_preference_order' => $preference->preference_order,
                        'student_average' => $average,
                        'allocation_rank' => $allocationRank,
                        'allocation_method' => 'automatic_by_merit',
                        'allocation_notes' => "Allocated based on academic merit (average: {$average}) and preference order",
                        'status' => 'tentative',
                    ]);

                    $allocatedSubjects[] = $preference->subject_id;
                    $allocated = true;
                    $allocationRank++;
                    break;
                }
            }

            if (!$allocated) {
                $availableSubjects = Subject::validated()
                    ->whereNotIn('id', $allocatedSubjects)
                    ->first();

                if ($availableSubjects) {
                    SubjectAllocation::create([
                        'allocation_deadline_id' => $deadline->id,
                        'student_id' => $student->id,
                        'subject_id' => $availableSubjects->id,
                        'student_preference_order' => null,
                        'student_average' => $average,
                        'allocation_rank' => $allocationRank,
                        'allocation_method' => 'automatic_fallback',
                        'allocation_notes' => "No preferred subjects available. Allocated to available subject.",
                        'status' => 'tentative',
                    ]);

                    $allocatedSubjects[] = $availableSubjects->id;
                    $allocationRank++;
                }
            }
        }
    }
}