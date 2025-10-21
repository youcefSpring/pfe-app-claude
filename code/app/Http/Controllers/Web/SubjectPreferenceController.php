<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SubjectPreference;
use App\Models\Subject;
use App\Models\AllocationDeadline;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubjectPreferenceController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $preferences = $user->subjectPreferences()
            ->with('subject')
            ->orderBy('preference_order')
            ->get();

        $currentDeadline = AllocationDeadline::active()->first();
        $canModifyPreferences = $currentDeadline &&
                               $currentDeadline->canStudentsChoose();

        return view('preferences.index', compact('preferences', 'currentDeadline', 'canModifyPreferences'));
    }

    public function create(): View
    {
        $currentDeadline = AllocationDeadline::active()->first();

        if (!$currentDeadline || !$currentDeadline->canStudentsChoose()) {
            return redirect()->route('preferences.index')
                ->with('error', 'Preference selection period has ended.');
        }

        $user = Auth::user();
        $existingPreferences = $user->subjectPreferences()->pluck('subject_id');

        $availableSubjects = Subject::validated()
            ->whereNotIn('id', $existingPreferences)
            ->get();

        return view('preferences.create', compact('availableSubjects', 'currentDeadline'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $currentDeadline = AllocationDeadline::active()->first();

        if (!$currentDeadline || !$currentDeadline->canStudentsChoose()) {
            return redirect()->route('preferences.index')
                ->with('error', 'Preference selection period has ended.');
        }

        $validated = $request->validate([
            'subject_preferences' => 'required|array|min:1|max:10',
            'subject_preferences.*' => 'required|exists:subjects,id',
        ]);

        $subjectIds = $validated['subject_preferences'];

        if (count($subjectIds) !== count(array_unique($subjectIds))) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Cannot select the same subject multiple times.');
        }

        DB::transaction(function () use ($user, $subjectIds, $currentDeadline) {
            $user->subjectPreferences()->delete();

            foreach ($subjectIds as $order => $subjectId) {
                SubjectPreference::create([
                    'allocation_deadline_id' => $currentDeadline->id,
                    'student_id' => $user->id,
                    'subject_id' => $subjectId,
                    'preference_order' => $order + 1,
                    'status' => 'draft',
                ]);
            }
        });

        return redirect()->route('preferences.index')
            ->with('success', __('app.subject_preferences_saved'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $currentDeadline = AllocationDeadline::active()->first();

        if (!$currentDeadline || !$currentDeadline->canStudentsChoose()) {
            return redirect()->route('preferences.index')
                ->with('error', 'Preference selection period has ended.');
        }

        $validated = $request->validate([
            'subject_preferences' => 'required|array|min:1|max:10',
            'subject_preferences.*' => 'required|exists:subjects,id',
        ]);

        $subjectIds = $validated['subject_preferences'];

        if (count($subjectIds) !== count(array_unique($subjectIds))) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Cannot select the same subject multiple times.');
        }

        DB::transaction(function () use ($user, $subjectIds, $currentDeadline) {
            $user->subjectPreferences()->delete();

            foreach ($subjectIds as $order => $subjectId) {
                SubjectPreference::create([
                    'allocation_deadline_id' => $currentDeadline->id,
                    'student_id' => $user->id,
                    'subject_id' => $subjectId,
                    'preference_order' => $order + 1,
                    'status' => 'draft',
                ]);
            }
        });

        return redirect()->route('preferences.index')
            ->with('success', __('app.subject_preferences_updated'));
    }

    public function destroy(): RedirectResponse
    {
        $user = Auth::user();
        $currentDeadline = AllocationDeadline::active()->first();

        if (!$currentDeadline || !$currentDeadline->canStudentsChoose()) {
            return redirect()->route('preferences.index')
                ->with('error', 'Preference selection period has ended.');
        }

        $user->subjectPreferences()->delete();

        return redirect()->route('preferences.index')
            ->with('success', __('app.all_preferences_cleared'));
    }

    public function submit(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $currentDeadline = AllocationDeadline::active()->first();

        if (!$currentDeadline || !$currentDeadline->canStudentsChoose()) {
            return redirect()->route('preferences.index')
                ->with('error', 'Preference selection period has ended.');
        }

        $preferences = $user->subjectPreferences();

        if ($preferences->count() === 0) {
            return redirect()->route('preferences.index')
                ->with('error', 'Please add at least one subject preference before submitting.');
        }

        $preferences->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('preferences.index')
            ->with('success', __('app.preferences_submitted_final'));
    }
}