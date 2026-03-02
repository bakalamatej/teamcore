<?php

namespace App\Http\Controllers;

use App\Models\Sport;
use App\Http\Requests\SportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SportController extends Controller
{
    // List all sports
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $sports = Sport::query();

        // Search by name
        if ($request->filled('search')) {
            $sports->where('name', 'like', '%' . $request->search . '%');
        }

        $sports = $sports->paginate(10);

        return view('panel.sports.index', compact('sports'));
    }

    // Show create form
    public function create()
    {
        $this->authorizeAdmin();

        return view('panel.sports.create');
    }

    // Store new sport
    public function store(SportRequest $request)
    {
        Sport::create($request->validated());

        return redirect()->route('panel.sports.index')->with('success', 'Sport created successfully!');
    }

    // Show edit form
    public function edit(Sport $sport)
    {
        $this->authorizeAdmin();

        return view('panel.sports.edit', compact('sport'));
    }

    // Update sport
    public function update(SportRequest $request, Sport $sport)
    {
        $sport->update($request->validated());

        return redirect()->route('panel.sports.index')->with('success', 'Sport updated successfully!');
    }

    // Delete sport
    public function destroy(Sport $sport)
    {
        $this->authorizeAdmin();

        $sport->delete();

        return redirect()->route('panel.sports.index')->with('success', 'Sport deleted successfully!');
    }

    // Helper: Check if user is admin
    private function authorizeAdmin()
    {
        if (!Auth::user() || Auth::user()->isAdmin() === false) {
            abort(403, 'Unauthorized');
        }
    }
}
