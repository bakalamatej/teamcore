<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case APPROVED = 'approved';
    case CANCELED = 'canceled';
    case CONVERTED = 'converted';
}