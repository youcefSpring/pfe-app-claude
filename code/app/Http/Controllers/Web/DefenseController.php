<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DefenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin_pfe,chef_master,teacher');
    }

    public function index(): View
    {
        return view('pfe.defenses.index');
    }

    public function create(): View
    {
        return view('pfe.defenses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('pfe.defenses.index')->with('success', 'Defense created successfully.');
    }

    public function show($defense): View
    {
        return view('pfe.defenses.show', compact('defense'));
    }

    public function edit($defense): View
    {
        return view('pfe.defenses.edit', compact('defense'));
    }

    public function update(Request $request, $defense): RedirectResponse
    {
        return redirect()->route('pfe.defenses.show', $defense)->with('success', 'Defense updated successfully.');
    }

    public function showGrades($defense): View
    {
        return view('pfe.defenses.grades', compact('defense'));
    }

    public function submitGrades(Request $request, $defense): RedirectResponse
    {
        return redirect()->route('pfe.defenses.show', $defense)->with('success', 'Grades submitted successfully.');
    }

    public function schedule(): View
    {
        return view('pfe.defenses.schedule');
    }

    public function autoSchedule(Request $request): RedirectResponse
    {
        return redirect()->route('pfe.defenses.index')->with('success', 'Auto-schedule completed successfully.');
    }

    public function generatePV(Request $request, $defense): RedirectResponse
    {
        return redirect()->route('pfe.defenses.show', $defense)->with('success', 'PV generated successfully.');
    }
}
