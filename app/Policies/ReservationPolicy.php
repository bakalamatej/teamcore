<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use App\Enums\ReservationStatus;

class ReservationPolicy extends Policy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->is_admin) {
            return true;
        }
        return null;
    }

    /**
     * Determine if the user can view any reservations.
     */
    public function viewAny(User $user): bool
    {
        return $this->isMember($user);
    }

    /**
     * Determine if the user can view the reservation.
     */
    public function view(User $user, Reservation $reservation): bool
    {
        // Creator can view
        if ($this->isReservationCreator($user, $reservation->created_by_member_club_id)) {
            return true;
        }

        // Club members can view
        return $this->isClubMember($user, $reservation->club_id);
    }

    /**
     * Determine if the user can create reservations.
     */
    public function create(User $user): bool
    {
        return $this->isMember($user);
    }

    /**
     * Determine if the user can update the reservation.
     */
    public function update(User $user, Reservation $reservation): bool
    {
        // Only pending reservations can be updated
        if ($reservation->status !== ReservationStatus::PENDING) {
            return false;
        }

        return $this->isReservationCreator($user, $reservation->created_by_member_club_id);
    }

    /**
     * Determine if the user can delete the reservation.
     */
    public function delete(User $user, Reservation $reservation): bool
    {
        // Only pending reservations can be deleted
        if ($reservation->status !== ReservationStatus::PENDING) {
            return false;
        }

        return $this->isReservationCreator($user, $reservation->created_by_member_club_id);
    }

    /**
     * Determine if the user can approve the reservation.
     */
    public function approve(User $user, Reservation $reservation): bool
    {
        return $this->isCoachInClub($user, $reservation->club_id);
    }

    /**
     * Determine if the user can reject the reservation.
     */
    public function reject(User $user, Reservation $reservation): bool
    {
        return $this->isCoachInClub($user, $reservation->club_id);
    }

    /**
     * Determine if the user can cancel the reservation.
     */
    public function cancel(User $user, Reservation $reservation): bool
    {
        return $this->isReservationCreator($user, $reservation->created_by_member_club_id);
    }
}
