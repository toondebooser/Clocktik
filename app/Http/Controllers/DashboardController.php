<?php

namespace App\Http\Controllers;


use App\Models\Timelog;
use App\Models\Timesheet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function userDashboard(Request $request)
    {
        $userRow = Timelog::where('UserId',auth()->user()->id)->first();

        $timesheetCheck = Timesheet::where('UserId', auth()->user()->id)
            ->whereMonth('Month', now('Europe/Brussels'))
            ->whereDay('Month', now('Europe/Brussels'))
            ->whereYear('Month',now('Europe/Brussels'))
            ->first();

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
        $start = $userRow->EndBreak? $userRow->EndBreak : $userRow->StartWork;
        $startBreak = $userRow->StartBreak;
        $breakHours = $userRow->BreakHours;
        $workedHours = $userRow->RegularHours;
        $lastWorkedDate = Carbon::parse($userRow->StartWork, "Europe/Brussels");

        return view('dashboard', ['user' => auth()->user(),'lastWorkedDate' => $lastWorkedDate, 'workedHours' => $workedHours ,'breakHours' => $breakHours ,'startBreak'=> $startBreak, 'start' => $start , 'shiftStatus' => $shiftStatus, 'breakStatus' => $breakStatus, 'userNote' => $userNote]);
    }

    
  
}
