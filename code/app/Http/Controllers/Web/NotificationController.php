<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('pfe.notifications.index');
    }

    public function markAsRead(Request $request, string $id)
    {
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(Request $request)
    {
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(string $id)
    {
        return redirect()->back()->with('success', 'Notification deleted.');
    }

    public function preferences()
    {
        return view('pfe.notifications.preferences');
    }

    public function updatePreferences(Request $request)
    {
        return redirect()->route('pfe.notifications.preferences')->with('success', 'Preferences updated.');
    }
}