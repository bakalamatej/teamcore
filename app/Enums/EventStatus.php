<?php

namespace App\Enums;

enum EventStatus: string
{
    case SCHEDULED = 'scheduled';
    case ONGOING = 'ongoing';
    case FINISHED = 'finished';
    case CANCELED = 'canceled';
}
