<?php

namespace App\Http\Controllers;

use App\Models\StudentAlert;
use App\Models\StudentMark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Only get marks created by admin/teachers, not registration marks
        $marks = StudentMark::where('user_id', $user->id)
            ->where('created_by', '!=', $user->id) // Exclude self-created marks (registration marks)
            ->whereNotIn('subject_name', [
                'Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5',
                'S1 - Previous Year', 'S2 - Previous Year', 'S3 - Previous Year', 'S4 - Previous Year', 'S5 - Previous Year'
            ]) // Extra safety: exclude known registration subject patterns
            ->orderBy('created_at', 'desc')
            ->get();

        $alerts = StudentAlert::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.dashboard', compact('marks', 'alerts'));
    }

    public function storeAlert(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        StudentAlert::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', __('app.alert_sent_successfully'));
    }
}
