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
        // Check if UserPolicy exists and use it
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->when($request->filled('search'), function($q) use ($request) {
                return $q->whereHas('member', function($q) use ($request) {
                    $q->where('first_name', 'like', '%' . $request->input('search') . '%')
                      ->orWhere('last_name', 'like', '%' . $request->input('search') . '%');
                })->orWhere('email', 'like', '%' . $request->input('search') . '%');
            })
            ->with('member')
            ->paginate(15);

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

        // Validate user and member data
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'first_name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'phone_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'is_admin' => 'nullable|boolean',
        ]);

        // Update user email and is_admin
        $user->update($request->only(['email', 'is_admin']));

        // Update member data if member exists
        if ($user->member) {
            $user->member->update($request->only(['first_name', 'last_name', 'phone_number', 'date_of_birth']));
        }

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