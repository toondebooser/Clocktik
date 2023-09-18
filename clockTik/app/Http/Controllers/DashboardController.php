<?php

namespace App\Http\Controllers;


use App\Models\Timelog;

class DashboardController extends Controller
{
    public function userDashboard()
    {
        $userRow = Timelog::find(auth()->user()->id);

        $shiftStatus = $userRow->ShiftStatus;
        $breakStatus = $userRow->BreakStatus;

        return view('dashboard', ['user' => auth()->user(), 'shiftStatus' => $shiftStatus, 'breakStatus' => $breakStatus]);
    }

    
  
}
