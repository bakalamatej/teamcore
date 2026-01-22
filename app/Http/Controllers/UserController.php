<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // List all users with search and role filtering (admin only)
    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $query = User::query();

        // Search by name or email
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        // Filter by role (player/coach/admin)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->with('member')->paginate(15);

        return view('panel.users.index', compact('users'));
    }

    // Display user details (admin only)
    public function show(User $user)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $user->load('member');
        return view('panel.users.show', compact('user'));
    }

    // Show edit user form (admin only)
    public function edit(User $user)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $user->load('member');
        return view('panel.users.edit', compact('user'));
    }

    // Update user (admin only)
    public function update(Request $request, User $user)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Validate user data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:player,coach,admin',
        ]);

        $user->update($request->only(['name', 'email', 'role']));

        // Return JSON for AJAX or redirect
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'User updated']);
        }

        return redirect()->route('panel.users.index')->with('success', 'User updated successfully!');
    }

    // Delete user (admin only, cannot delete self)
    public function destroy(User $user)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return redirect()->route('panel.users.index')->with('error', 'Cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('panel.users.index')->with('success', 'User deleted successfully!');
    }
}