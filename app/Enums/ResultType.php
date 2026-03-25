<?php

namespace App\Enums;

enum ResultType: string
{
    case SCORE = 'score';
    case TIME = 'time';
    case DISTANCE = 'distance';
    case POINTS = 'points';
    case CUSTOM = 'custom';
    case GOALS  = 'goals';
}
