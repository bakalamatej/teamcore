<?php
namespace App\Policies;
use App\Models\Reservation;
use App\Models\User;
use App\Enums\ReservationStatus;
use App\Enums\MemberClubRole;

class ReservationPolicy extends Policy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->is_admin) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $this->isMember($user);
    }

    public function view(User $user, Reservation $reservation): bool
    {
        if ($this->isReservationCreator($user, $reservation->created_by_member_club_id)) {
            return true;
        }

        if ($this->isClubMember($user, $reservation->club_id)) {
            return true;
        }

        // Coach can view reservations on sport fields that support their club's sport
        $membership = $user->activeMembership();
        if ($membership && $membership->role === MemberClubRole::COACH) {
            return \App\Models\SportField::where('sport_field_id', $reservation->sport_field_id)
                ->whereHas('sports', fn($q) => $q->where('sports.sport_id', $membership->sport_id))
                ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $this->isMember($user);
    }

    public function update(User $user, Reservation $reservation): bool
    {
        if ($reservation->status !== ReservationStatus::PENDING) {
            return false;
        }
        return $this->isReservationCreator($user, $reservation->created_by_member_club_id);
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        if ($reservation->status !== ReservationStatus::PENDING) {
            return false;
        }
        return $this->isReservationCreator($user, $reservation->created_by_member_club_id);
    }

    public function approve(User $user, Reservation $reservation): bool
    {
        return $this->isCoachInClub($user, $reservation->club_id);
    }

    public function reject(User $user, Reservation $reservation): bool
    {
        return $this->isCoachInClub($user, $reservation->club_id);
    }

    public function cancel(User $user, Reservation $reservation): bool
    {
        return $this->isReservationCreator($user, $reservation->created_by_member_club_id);
    }
}