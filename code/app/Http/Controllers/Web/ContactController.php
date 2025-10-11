<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessage;

class ContactController extends Controller
{
    /**
     * Show the contact form
     */
    public function show()
    {
        // Get the main teacher/admin contact (you can customize this logic)
        $teacher = User::where('role', 'teacher')
            ->orWhere('role', 'admin')
            ->first();

        return view('public.contact', compact('teacher'));
    }

    /**
     * Show the about page
     */
    public function about()
    {
        // Get the main teacher/admin profile (you can customize this logic)
        $teacher = User::where('role', 'teacher')
            ->orWhere('role', 'admin')
            ->first();

        return view('public.about', compact('teacher'));
    }

    /**
     * Handle contact form submission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject_type' => 'required|in:general_inquiry,collaboration,course_info,student_inquiry,media_interview,speaking_engagement,other',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        try {
            // Get the recipient (main contact person)
            $recipient = User::where('role', 'teacher')
                ->orWhere('role', 'admin')
                ->first();

            if ($recipient && $recipient->email) {
                // Send email notification
                Mail::to($recipient->email)->send(new ContactMessage($validated));
            }

            return back()->with('success', __('app.contact_success_message'));
        } catch (\Exception $e) {
            return back()->with('error', __('app.contact_error_message'));
        }
    }
}