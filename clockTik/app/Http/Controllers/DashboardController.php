<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function currentUser()
    {
        return view('dashboard', ['user' => auth()->user()]);
    }
}
