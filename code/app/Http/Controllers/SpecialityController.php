<?php

namespace App\Http\Controllers;

use App\Models\Speciality;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SpecialityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $specialities = Speciality::with('users')
            ->orderBy('level')
            ->orderBy('name')
            ->paginate(15);

        return view('specialities.index', compact('specialities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('specialities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10|unique:specialities,code',
            'level' => 'required|in:licence,master,ingenieur',
            'academic_year' => 'required|string|max:9',
            'semester' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // Set current academic year if not provided
        if (!$validated['academic_year']) {
            $validated['academic_year'] = Speciality::getCurrentAcademicYear();
        }

        $validated['is_active'] = $request->has('is_active');

        Speciality::create($validated);

        return redirect()->route('specialities.index')
            ->with('success', __('Speciality created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Speciality $speciality): View
    {
        $speciality->load(['users' => function($query) {
            $query->orderBy('name');
        }]);

        $studentCount = $speciality->students()->count();
        $teacherCount = $speciality->users()->where('role', 'teacher')->count();

        return view('specialities.show', compact('speciality', 'studentCount', 'teacherCount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Speciality $speciality): View
    {
        return view('specialities.edit', compact('speciality'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Speciality $speciality): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10|unique:specialities,code,' . $speciality->id,
            'level' => 'required|in:licence,master,ingenieur',
            'academic_year' => 'required|string|max:9',
            'semester' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $speciality->update($validated);

        return redirect()->route('specialities.index')
            ->with('success', __('Speciality updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Speciality $speciality): RedirectResponse
    {
        // Check if speciality has users
        if ($speciality->users()->count() > 0) {
            return redirect()->route('specialities.index')
                ->with('error', __('Cannot delete speciality with assigned users.'));
        }

        $speciality->delete();

        return redirect()->route('specialities.index')
            ->with('success', __('Speciality deleted successfully.'));
    }

    /**
     * Toggle the active status of a speciality.
     */
    public function toggleActive(Speciality $speciality): RedirectResponse
    {
        $speciality->update([
            'is_active' => !$speciality->is_active
        ]);

        $status = $speciality->is_active ? 'activated' : 'deactivated';

        return redirect()->route('specialities.index')
            ->with('success', __("Speciality {$status} successfully."));
    }
}
