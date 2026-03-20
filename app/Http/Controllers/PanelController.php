<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PanelController extends Controller
{
    // Display user's profile/dashboard
    public function index(Request $request)
    {
        return view('panel.update.index', [
            'user' => $request->user(),
        ]);
    }

    // Update user profile (email and member information)
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Update user email
        $user = $request->user();
        $user->fill($request->only('email'));

        // Reset email verification if email changed
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        // Update or create member information
        if ($user->member) {
            $user->member->update($request->only(['first_name', 'last_name', 'phone', 'date_of_birth']));
        } else {
            // Create member if doesn't exist
            $user->member()->create($request->only(['first_name', 'last_name', 'phone', 'date_of_birth']));
        }

        return Redirect::route('panel.update.index')
            ->with('status', 'profile-updated');
    }

    // Delete user account (requires password confirmation)
    public function destroy(Request $request): RedirectResponse
    {
        // Verify password before deletion
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Logout and delete user
        Auth::logout();
        $user->delete();

        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
