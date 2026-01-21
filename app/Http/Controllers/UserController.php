<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $query = User::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(15);

        return view('panel.users.index', compact('users'));
    }

    public function show(User $user)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('panel.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('panel.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:player,coach,admin',
        ]);

        $user->update($request->only(['name', 'email', 'role']));

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'User updated']);
        }

        return redirect()->route('panel.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($user->id === Auth::id()) {
            return redirect()->route('panel.users.index')->with('error', 'Cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('panel.users.index')->with('success', 'User deleted successfully!');
    }
}