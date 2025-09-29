<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('pfe.profile.index');
    }

    public function edit()
    {
        return view('pfe.profile.edit');
    }

    public function update(Request $request)
    {
        return redirect()->route('pfe.profile.index')->with('success', 'Profile updated successfully.');
    }

    public function destroy(Request $request)
    {
        return redirect()->route('login')->with('success', 'Account deleted successfully.');
    }
}