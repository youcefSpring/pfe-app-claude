<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Speciality;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class SpecialityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Speciality::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('level', 'like', "%{$search}%");
            });
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->get('level'));
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->get('academic_year'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        $specialities = $query->latest()->paginate(15);

        // Get unique academic years for filter
        $academicYears = Speciality::distinct('academic_year')
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');

        return view('admin.specialities.index', compact('specialities', 'academicYears'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.specialities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:20'],
            'level' => ['required', 'string', Rule::in(array_keys(Speciality::LEVELS))],
            'academic_year' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Le nom de la spécialité est obligatoire.',
            'level.required' => 'Le niveau est obligatoire.',
            'level.in' => 'Le niveau sélectionné n\'est pas valide.',
            'academic_year.required' => 'L\'année académique est obligatoire.',
            'academic_year.regex' => 'L\'année académique doit être au format YYYY/YYYY.',
        ]);

        // Handle checkbox (is_active)
        $validated['is_active'] = $request->has('is_active');

        // Check for uniqueness
        $exists = Speciality::where('name', $validated['name'])
            ->where('level', $validated['level'])
            ->where('academic_year', $validated['academic_year'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'name' => 'Cette spécialité existe déjà pour ce niveau et cette année académique.'
            ])->withInput();
        }

        Speciality::create($validated);

        return redirect()
            ->route('admin.specialities.index')
            ->with('success', __('app.speciality_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Speciality $speciality): View
    {
        $speciality->loadCount('students');

        return view('admin.specialities.show', compact('speciality'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Speciality $speciality): View
    {
        return view('admin.specialities.edit', compact('speciality'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Speciality $speciality): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:20'],
            'level' => ['required', 'string', Rule::in(array_keys(Speciality::LEVELS))],
            'academic_year' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Le nom de la spécialité est obligatoire.',
            'level.required' => 'Le niveau est obligatoire.',
            'level.in' => 'Le niveau sélectionné n\'est pas valide.',
            'academic_year.required' => 'L\'année académique est obligatoire.',
            'academic_year.regex' => 'L\'année académique doit être au format YYYY/YYYY.',
        ]);

        // Handle checkbox (is_active)
        $validated['is_active'] = $request->has('is_active');

        // Check for uniqueness (excluding current record)
        $exists = Speciality::where('name', $validated['name'])
            ->where('level', $validated['level'])
            ->where('academic_year', $validated['academic_year'])
            ->where('id', '!=', $speciality->id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'name' => 'Cette spécialité existe déjà pour ce niveau et cette année académique.'
            ])->withInput();
        }

        $speciality->update($validated);

        return redirect()
            ->route('admin.specialities.index')
            ->with('success', __('app.speciality_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Speciality $speciality): RedirectResponse
    {
        // Check if speciality has students
        if ($speciality->students()->count() > 0) {
            return back()->with('error', __('app.cannot_delete_speciality_with_students'));
        }

        $speciality->delete();

        return redirect()
            ->route('admin.specialities.index')
            ->with('success', __('app.speciality_deleted_successfully'));
    }

    /**
     * Toggle the active status of the speciality.
     */
    public function toggleStatus(Speciality $speciality): RedirectResponse
    {
        $speciality->update(['is_active' => !$speciality->is_active]);

        $status = $speciality->is_active ? 'activée' : 'désactivée';

        return back()->with('success', __('app.speciality_status_updated_successfully', ['status' => $status]));
    }

    /**
     * Get current academic year for AJAX requests.
     */
    public function getCurrentAcademicYear()
    {
        return response()->json([
            'academic_year' => Speciality::getCurrentAcademicYear()
        ]);
    }
}