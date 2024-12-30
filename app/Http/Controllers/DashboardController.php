<?php

namespace App\Http\Controllers;


use App\Models\Timelog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function userDashboard(Request $request)
    {
        $userRow = Timelog::where('UserId',auth()->user()->id)->first();
        $userNoteInput = $request->input('userNote');
        if($userNoteInput !== null || $userNoteInput === '')
        {
            $userNoteInput === ''? $userRow->userNote = null : null;
            $userRow->userNote = $userNoteInput;
            $userRow->save();
        }

        $userNote = $userRow->userNote;
        $shiftStatus = $userRow->ShiftStatus;
        $breakStatus = $userRow->BreakStatus;
        $start = $userRow->StartWork;
        $startBreak = $userRow->StartBreak;
        $breakHours = $userRow->BreakHours;
        $workedHours = $userRow->RegularHours;

        return view('dashboard', ['user' => auth()->user(),'workedHours' => $workedHours ,'breakHours' => $breakHours ,'startBreak'=> $startBreak, 'start' => $start , 'shiftStatus' => $shiftStatus, 'breakStatus' => $breakStatus, 'userNote' => $userNote]);
    }

    
  
}
