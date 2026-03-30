<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\CoachEvaluation;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // List all users with search and role filtering (admin only)
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->search($request->input('search'))
            ->when($request->input('is_admin') === '1', fn($q) => $q->admins())
            ->when($request->input('is_admin') === '0', fn($q) => $q->regularUsers())
            ->orderBy('email')
            ->with('member')
            ->paginate(8);

        $users->getCollection()->transform(function ($user) {
            $user->setAttribute('primaryRole', $user->getRole());
            return $user;
        });

        if ($request->ajax()) {
            return view('panel.admin.users._table', compact('users'));
        }

        return view('panel.admin.users.index', compact('users'));
    }

    // Show create user form (admin only)
    public function create()
    {
        $this->authorize('create', User::class);

        return view('panel.admin.users.create');
    }

    // Store new user (admin only)
    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        try {
            $validated = $request->validated();
            $user = User::create([
                'email' => $validated['email'],
                'password_hash' => $validated['password'],
                'is_admin' => $validated['is_admin'] ?? false,
            ]);


            // Create member profile if data provided
            if ($request->filled('first_name') || $request->filled('last_name')) {
                $user->member()->create($request->only(['first_name', 'last_name', 'phone', 'date_of_birth']));
            }

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'User created', 'user' => $user], 201);
            }

            return redirect()->route('panel.admin.users.index')->with('success', 'User created successfully!');
        } catch (\Illuminate\Database\QueryException $exception) {
            return back()->withInput()->withErrors(['error' => 'Unable to create user.']);
        }

    }

    // Display user details (admin only)
    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load('member');

        $primaryRole = $user->getRole();

        $activeClubsCount = 0;
        $activeEventsCount = 0;

        if ($user->member) {
            $activeClubsCount = $user->member->activeClubs()->count();
            $activeEventsCount = $user->member->activeEventsQuery()->count();
        }

        return view('panel.admin.users.show', compact(
            'user',
            'primaryRole',
            'activeClubsCount',
            'activeEventsCount'
        ));
    }

    // Show edit user form (admin only)
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $user->load('member');
        return view('panel.admin.users.edit', compact('user'));
    }

    // Update user (admin only)
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        try {
            // Update user email and is_admin
            $user->update($request->only(['email', 'is_admin']));

            // Update member data if member exists
            if ($user->member) {
                $user->member->update($request->only(['first_name', 'last_name', 'phone', 'date_of_birth']));
            }

            // Return JSON for AJAX or redirect
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'User updated']);
            }

            return redirect()->route('panel.admin.users.index')->with('success', 'User updated successfully!');
        } catch (\Illuminate\Database\QueryException $exception) {
            return back()->withInput()->withErrors(['error' => 'Unable to update user.']);
        }
    }

    // Delete user (admin only, cannot delete self)
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        try {
            if ($user->member) {
                $memberId = $user->member->member_id;
                CoachEvaluation::where('coach_member_id', $memberId)->delete();
                
                $user->member->delete();
            }

            $user->delete();
            return redirect()->route('panel.admin.users.index')->with('success', 'User deleted successfully!');

        } catch (\Illuminate\Database\QueryException $exception) {
            return back()->withErrors(['error' => 'Unable to delete user.']);
        }
    }
}