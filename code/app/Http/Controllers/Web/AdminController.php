<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin_pfe,chef_master');
    }

    public function index()
    {
        return view('pfe.admin.users.index');
    }

    public function create()
    {
        return view('pfe.admin.users.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('pfe.admin.users.index')->with('success', 'User created successfully.');
    }

    public function show(string $id)
    {
        return view('pfe.admin.users.show', compact('id'));
    }

    public function edit(string $id)
    {
        return view('pfe.admin.users.edit', compact('id'));
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('pfe.admin.users.show', $id)->with('success', 'User updated successfully.');
    }

    public function destroy(string $id)
    {
        return redirect()->route('pfe.admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function editRoles(string $id)
    {
        return view('pfe.admin.users.roles', compact('id'));
    }

    public function updateRoles(Request $request, string $id)
    {
        return redirect()->route('pfe.admin.users.show', $id)->with('success', 'User roles updated successfully.');
    }

    public function settings()
    {
        return view('pfe.admin.settings');
    }

    public function updateSettings(Request $request)
    {
        return redirect()->route('pfe.admin.settings')->with('success', 'Settings updated successfully.');
    }
}
