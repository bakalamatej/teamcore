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
        return view('panel.index', [
            'user' => $request->user(),
        ]);
    }

    // Update user profile (name, email)
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        // Reset email verification if email changed
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('panel.index')
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
