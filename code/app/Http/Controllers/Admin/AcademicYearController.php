<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->paginate(10);
        $currentYear = AcademicYear::getCurrentYear();

        return view('admin.academic-years.index', compact('academicYears', 'currentYear'));
    }

    public function create()
    {
        return view('admin.academic-years.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|string|max:10|unique:academic_years,year',
            'title' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'is_current' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->has('is_current') && $request->is_current) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
        }

        AcademicYear::create([
            'year' => $request->year,
            'title' => $request->title ?: "Academic Year {$request->year}",
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'is_current' => $request->has('is_current') ? $request->is_current : false,
            'status' => 'draft',
        ]);

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('app.academic_year_created_successfully'));
    }

    public function show(AcademicYear $academicYear)
    {
        $academicYear->load(['subjects', 'teams', 'projects', 'defenses']);
        $statistics = $academicYear->statistics ?: $academicYear->calculateStatistics();

        return view('admin.academic-years.show', compact('academicYear', 'statistics'));
    }

    public function edit(AcademicYear $academicYear)
    {
        if (!$academicYear->canBeEdited()) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', __('app.cannot_edit_completed_academic_year'));
        }

        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        if (!$academicYear->canBeEdited()) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', __('app.cannot_edit_completed_academic_year'));
        }

        $validator = Validator::make($request->all(), [
            'year' => 'required|string|max:10|unique:academic_years,year,' . $academicYear->id,
            'title' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'is_current' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->has('is_current') && $request->is_current) {
            AcademicYear::where('is_current', true)->where('id', '!=', $academicYear->id)
                ->update(['is_current' => false]);
        }

        $academicYear->update([
            'year' => $request->year,
            'title' => $request->title ?: "Academic Year {$request->year}",
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'is_current' => $request->has('is_current') ? $request->is_current : $academicYear->is_current,
        ]);

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('app.academic_year_updated_successfully'));
    }

    public function activate(AcademicYear $academicYear)
    {
        if ($academicYear->status !== 'draft') {
            return redirect()->route('admin.academic-years.index')
                ->with('error', __('app.cannot_activate_non_draft_year'));
        }

        AcademicYear::where('status', 'active')->update(['status' => 'completed']);
        AcademicYear::where('is_current', true)->update(['is_current' => false]);

        $academicYear->update([
            'status' => 'active',
            'is_current' => true,
        ]);

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('app.academic_year_activated_successfully'));
    }

    public function end(AcademicYear $academicYear)
    {
        if (!$academicYear->canBeEnded()) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', __('app.cannot_end_academic_year'));
        }

        $success = $academicYear->endYear(Auth::user());

        if ($success) {
            return redirect()->route('admin.academic-years.index')
                ->with('success', __('app.academic_year_ended_successfully'));
        }

        return redirect()->route('admin.academic-years.index')
            ->with('error', __('app.failed_to_end_academic_year'));
    }

    public function history()
    {
        $completedYears = AcademicYear::completed()
            ->orderBy('year', 'desc')
            ->paginate(10);

        return view('admin.academic-years.history', compact('completedYears'));
    }

    public function statistics(AcademicYear $academicYear)
    {
        $statistics = $academicYear->statistics ?: $academicYear->calculateStatistics();

        return response()->json([
            'statistics' => $statistics,
            'year' => $academicYear->year,
            'title' => $academicYear->title,
        ]);
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->isActive() || $academicYear->isCurrent()) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', __('app.cannot_delete_active_current_year'));
        }

        if ($academicYear->subjects()->exists() ||
            $academicYear->teams()->exists() ||
            $academicYear->projects()->exists() ||
            $academicYear->defenses()->exists()) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', __('app.cannot_delete_year_with_data'));
        }

        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')
            ->with('success', __('app.academic_year_deleted_successfully'));
    }
}
