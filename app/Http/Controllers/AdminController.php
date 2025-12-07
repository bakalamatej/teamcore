<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function clubs()
    {
        return view('admin.clubs');
    }

    public function users()
    {
        return view('admin.users');
    }

    public function events()
    {
        return view('admin.events');
    }

    public function types()
    {
        return view('admin.types');
    }

    public function fields()
    {
        return view('admin.fields');
    }
}
 