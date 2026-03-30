<?php

namespace App\Enums;

enum ResultType: string
{
    case SCORE = 'score';
    case TIME = 'time';
    case DISTANCE = 'distance';
    case POINTS = 'points';
    case SETS = 'sets';
    case ASSISTS = 'assists';
    case CUSTOM = 'custom';
    case GOALS  = 'goals';
}
