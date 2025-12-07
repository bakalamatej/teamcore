<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoachController extends Controller
{
    public function players()
    {
        return view('coach.players');
    }

    public function trainings()
    {
        return view('coach.trainings');
    }

    public function events()
    {
        return view('coach.events');
    }
}
