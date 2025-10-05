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

        $marks = StudentMark::where('user_id', $user->id)
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
