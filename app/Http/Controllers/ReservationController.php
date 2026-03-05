<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\SportField;
use App\Models\Club;
use App\Models\MemberClub;
use App\Http\Requests\ReservationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isCoach()) abort(403);

        $query = Reservation::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reservations = $query->with('sportField', 'club')->paginate(15);
        return view('reservations.index', compact('reservations'));
    }

    public function create()
    {
        $sportFields = SportField::all();
        $clubs = Club::all();
        $memberClubs = Auth::user()->member?->clubMemberships ?? collect();
        return view('reservations.create', compact('sportFields', 'clubs', 'memberClubs'));
    }

    public function store(ReservationRequest $request)
    {
        $reservation = Reservation::create($request->validated());
        return redirect()->route('reservations.show', $reservation)->with('success', 'Reservation created successfully.');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('sportField', 'club', 'createdByMemberClub');
        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isCoach()) abort(403);
        $sportFields = SportField::all();
        $clubs = Club::all();
        return view('reservations.edit', compact('reservation', 'sportFields', 'clubs'));
    }

    public function update(ReservationRequest $request, Reservation $reservation)
    {
        $reservation->update($request->validated());
        return redirect()->route('reservations.show', $reservation)->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isCoach()) abort(403);
        $reservation->delete();
        return redirect()->route('reservations.index')->with('success', 'Reservation deleted successfully.');
    }
}
